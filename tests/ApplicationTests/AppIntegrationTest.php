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


class AppIntegrationTest extends ApplicationTests
{
    public function testUnknownCommand_ReturnsErrorCode()
    {
        $app = $this->app(new Doubles\FakeDirectory());
        $this->assertNotEquals(0, $app->run('unknown', []));
    }

    public function testWithBackupDirectorySet_BackupFilesAreCopiedToThatDirectory()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);
        $backup  = new Doubles\FakeDirectory();

        $app->backup($backup);
        $app->run('init', []);

        $this->assertTrue($backup->file('README.md')->exists());
        $this->assertTrue($backup->file('composer.json')->exists());
    }

    public function testWithMetaDataFilenameSet_MetaDataIsSavedInThatFileInsidePackageDirectory()
    {
        $package = self::$files->directory('package');
        $app     = $this->app($package);

        $app->metaFile('dev/meta-data.json');
        $app->run('init', []);

        $this->assertTrue($package->file('dev/meta-data.json')->exists());
    }
}
