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

use Shudd3r\PackageFiles\Update;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class UpdateTest extends IntegrationTestCase
{
    public function testSynchronizedPackage_IsUpdated()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-synchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->command($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-updated');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testPackageWithoutMetaDataFile_IsNotUpdated()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-synchronized');
        $env     = $this->envSetup($package, $files->directory('template'), null, new Doubles\MockedFile(null));

        $this->command($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-synchronized');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    public function testDesynchronizedPackage_IsNotUpdated()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-desynchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->command($env)->execute();

        $expectedFiles = new Fixtures\ExampleFiles('example-files/package-desynchronized');
        $this->assertTrue($expectedFiles->hasSameFilesAs($package));
    }

    protected function command(RuntimeEnv $env): Command
    {
        $update = new Update($env);
        return $update->command([
            'repo'    => 'updated/repo',
            'package' => 'updated/package-name',
            'desc'    => 'Updated package description',
            'ns'      => 'Package\Updated'
        ]);
    }
}
