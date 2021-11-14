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


class UpdateTest extends ApplicationTests
{
    private array $updateArgs = [
        'update',
        '--local',
        'repo=updated/repo',
        'package=updated/package-name',
        'desc=Updated package description',
        'ns=Package\Updated',
        'email=updated@example.com'
    ];

    public function testUpdatingSynchronizedPackage_GeneratesUpdatedPackageFiles()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $this->assertSame(0, $app->run($this->args(...$this->updateArgs)));
        $this->assertSameFiles($package, 'package-updated');
    }

    public function testUpdatingPackageWithoutMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $package->removeFile('.github/skeleton.json');

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run($this->args(...$this->updateArgs)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testUpdatingDesynchronizedPackage_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-desynchronized');
        $app     = $this->app($package, true);

        $expected = $this->snapshot($package);
        $this->assertNotEquals(0, $app->run($this->args(...$this->updateArgs)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testUpdatingWithInvalidReplacements_AbortsExecutionWithoutSideEffects()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $expected = $this->snapshot($package);

        $args    = $this->updateArgs;
        $args[3] = 'package=invalid-package-name';

        $this->assertNotEquals(0, $app->run($this->args(...$args)));
        $this->assertSame($expected, $this->snapshot($package));
    }

    public function testRedundantDummyFiles_AreRemoved()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package, true);

        $package->addFile('docs/SOMETHING.md');
        $this->assertTrue($package->file('docs/.gitkeep')->exists());
        $app->run($this->args(...$this->updateArgs));
        $this->assertFalse($package->file('docs/.gitkeep')->exists());
    }
}
