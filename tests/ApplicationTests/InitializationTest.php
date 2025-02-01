<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\ApplicationTests;

use Shudd3r\Skeletons\Tests\ApplicationTests;
use Shudd3r\Skeletons\Application;


class InitializationTest extends ApplicationTests
{
    private array $initArgs = [
        'init',
        '-il',
        'repo=initial/repo',
        'package=Initial/Package-Name',
        'desc=Initial package description',
        'ns=Package\Initial',
        'email=initial@example.com'
    ];

    public function test_files_from_template_are_generated(): void
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $this->assertSame(0, $app->run($this->args(...$this->initArgs)));
        $this->assertSameFiles($package, 'package-initialized');
    }

    /** @dataProvider fileContentsBackupStrategy */
    public function test_backup_is_created_only_for_changed_non_empty_files(string $contents, bool $expectBackup): void
    {
        $package = self::$files->directory('package');
        $backup  = self::$files->directory();
        $app     = $this->app($package);

        $app->backup($backup);
        $package->removeFile('composer.json');
        if ($contents === '---match---') {
            $contents = self::$files->contentsOf('package-initialized/composer.json');
        }
        $package->addFile('composer.json', $contents);

        $app->run($this->args(...$this->initArgs));
        $this->assertSame($expectBackup, $backup->file('composer.json')->exists());
    }

    public function test_empty_template_files_are_created(): void
    {
        $package  = self::$files->directory();
        $template = self::$files->directory();
        $app      = new Application($package, $template, self::$terminal->reset());

        $template->addFile('empty.txt');
        $app->run($this->args(...$this->initArgs));
        $this->assertTrue($package->file('empty.txt')->exists());
    }

    public function test_existing_meta_data_file_aborts_execution_without_side_effects(): void
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $package->addFile('.package/skeleton.json');
        $app->metaFile('.package/skeleton.json');

        $expected = $this->snapshot($package);
        $this->assertSame(10, $app->run($this->args(...$this->initArgs)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function test_backup_file_overwrite_aborts_execution_without_side_effects(): void
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);
        $backup  = self::$files->directory();

        $app->backup($backup);
        $backup->addFile('README.md');

        $expected = $this->snapshot($package);
        $this->assertSame(34, $app->run($this->args(...$this->initArgs)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function test_invalid_replacement_aborts_execution_without_side_effects(): void
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $expected = $this->snapshot($package);

        $args    = $this->initArgs;
        $args[1] = '-l';
        $args[3] = 'package=invalid-package-name';
        $this->assertSame(6, $app->run($this->args(...$args)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function test_redundant_dummy_files_are_not_added(): void
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $package->addFile('docs/SOMETHING.md');
        $app->run($this->args(...$this->initArgs));
        $this->assertFalse($package->file('docs/.gitkeep')->exists());
    }
}
