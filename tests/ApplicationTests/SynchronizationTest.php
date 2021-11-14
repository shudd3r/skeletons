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


class SynchronizationTest extends ApplicationTests
{
    public function testSynchronizingPackage_GeneratesMissingAndDivergentFiles()
    {
        $package = self::$files->directory('package-desynchronized');
        $app     = $this->app($package);

        $this->assertEquals(0, $app->run($this->args('sync', '--local')));
        $this->assertSameFiles($package, 'package-after-sync');
    }

    /**
     * @dataProvider fileContentsBackupStrategy
     * @param string $contents
     * @param bool   $expectBackup
     */
    public function testSynchronizingPackage_CreatesBackupOnlyForMismatchedNonEmptyFiles(string $contents, bool $expectBackup)
    {
        $package = self::$files->directory('package-desynchronized');
        $backup  = new Doubles\FakeDirectory();
        $app     = $this->app($package);

        $app->backup($backup);
        $package->removeFile('composer.json');
        if ($contents === '---match---') {
            $contents = self::$files->contentsOf('package-synchronized/composer.json');
        }
        $package->addFile('composer.json', $contents);

        $app->run($this->args('sync'));
        $this->assertSame($expectBackup, $backup->file('composer.json')->exists());
    }

    public function testRedundantDummyFiles_AreRemoved()
    {
        $package = self::$files->directory('package-desynchronized');
        $app     = $this->app($package);

        $package->addFile('docs/SOMETHING.md');
        $this->assertTrue($package->file('docs/.gitkeep')->exists());
        $app->run($this->args('sync'));
        $this->assertFalse($package->file('docs/.gitkeep')->exists());
    }
}
