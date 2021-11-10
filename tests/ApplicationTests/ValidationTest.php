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
        $this->assertSame(0, $app->run($this->args('check')));

        $app = $this->app(self::$files->directory('package-synchronized'));
        $this->assertSame(0, $app->run($this->args('check')));
    }

    public function testSynchronizedPackageWithoutLocalFiles_IsValidForRemoteCheck()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->removeFile('.git/hooks/pre-commit');

        $this->assertSame(0, $app->run($this->args('check', '--remote')));
        $this->assertNotSame(0, $app->run($this->args('check')));
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $app = $this->app(self::$files->directory('package-desynchronized'));
        $this->assertNotEquals(0, $app->run($this->args('check')));
    }

    public function testPackageWithoutMetaDataFile_IsInvalid()
    {
        $package = self::$files->directory('package-synchronized');
        $app     = $this->app($package);

        $package->removeFile('.github/skeleton.json');
        $this->assertNotEquals(0, $app->run($this->args('check')));
    }
}