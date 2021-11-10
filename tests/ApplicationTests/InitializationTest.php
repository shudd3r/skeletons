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
use Shudd3r\Skeletons\Tests\Doubles;


class InitializationTest extends ApplicationTests
{
    private array $initArgs = [
        'init',
        '-i',
        'repo=initial/repo',
        'package=initial/package-name',
        'desc=Initial package description',
        'ns=Package\Initial'
    ];

    public function testInitialization_GeneratesFilesFromTemplate()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $this->assertSame(0, $app->run($this->args(...$this->initArgs)));
        $this->assertSameFiles($package, 'package-initialized');
    }

    /**
     * @dataProvider fileContentsBackupStrategy
     * @param string $contents
     * @param bool   $expectBackup
     */
    public function testInitialization_CreatesBackupOnlyForMismatchedNonEmptyFiles(string $contents, bool $expectBackup)
    {
        $package = self::$files->directory('package');
        $backup  = new Doubles\FakeDirectory();
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

    public function testInitializationWithExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $package->addFile('.package/skeleton.json');
        $app->metaFile('.package/skeleton.json');

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run($this->args(...$this->initArgs)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testInitializationThatCouldOverwriteBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);
        $backup  = new Doubles\FakeDirectory();

        $app->backup($backup);
        $backup->addFile('README.md');

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run($this->args(...$this->initArgs)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testInitializeWithInvalidReplacements_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $expected = $this->snapshot($package);

        $args    = $this->initArgs;
        $args[3] = 'package=invalid-package-name';
        $this->assertNotEquals(0, $app->run($this->args(...$args)));
        $this->assertSame($expected, $this->snapshot($package));
    }
}