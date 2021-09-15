<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests;

use Shudd3r\PackageFiles\Initialize;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class InitializeTest extends IntegrationTestCase
{
    public function testInitialization_GeneratesFilesFromTemplate()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->command($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-initialized');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package');
        $env     = $this->envSetup($package, $files->directory('template'), null, new Doubles\MockedFile());

        $this->command($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package');
        $backup  = new Doubles\FakeDirectory();
        $backup->addFile('README.md', 'anything');
        $env = $this->envSetup($package, $files->directory('template'), $backup);

        $this->command($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    protected function command(RuntimeEnv $env): Command
    {
        $initialize = new Initialize($env);
        return $initialize->command([
            'repo'    => 'initial/repo',
            'package' => 'initial/package-name',
            'desc'    => 'Initial package description',
            'ns'      => 'Package\Initial'
        ]);
    }
}
