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


class ValidationTest extends ApplicationTests
{
    public function testSynchronizedPackage_IsValidForLocalCheck()
    {
        $app = $this->app(self::$files->directory('package-initialized'));
        $this->assertSame(0, $app->run($this->args('check', '--local')));

        $app = $this->app(self::$files->directory('package-synchronized'));
        $this->assertSame(0, $app->run($this->args('check', '--local')));
    }

    public function testSynchronizedPackageWithoutLocalFiles_IsValidForDeployedFiles()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->removeFile('.git/hooks/pre-commit');

        $this->assertSame(0, $app->run($this->args('check')));
        $this->assertSame(1, $app->run($this->args('check', '--local')));
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $app = $this->app(self::$files->directory('package-desynchronized'));
        $this->assertSame(1, $app->run($this->args('check', '--local')));
    }

    public function testPackageWithoutMetaDataFile_IsInvalid()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->removeFile('.github/skeleton.json');
        $this->assertSame(10, $app->run($this->args('check', '--local')));
    }

    public function testPackageWithRedundantDummyFiles_IsInvalid()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->addFile('docs/SOMETHING.md');
        $this->assertTrue($package->file('docs/.gitkeep')->exists());
        $app->run($this->args('check'));
        $this->assertSame(1, $app->run($this->args('check')));
    }

    public function testPackageWithoutRedundantDummyFiles_IsValid()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->addFile('docs/SOMETHING.md');
        $package->addFile('docs/README.md');
        $package->removeFile('docs/.gitkeep');
        $this->assertFalse($package->file('docs/.gitkeep')->exists());
        $app->run($this->args('check'));
        $this->assertSame(0, $app->run($this->args('check')));
    }
}
