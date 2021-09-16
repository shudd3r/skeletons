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
        $env = $this->envSetup('package-synchronized');
        $this->command($env)->execute();
        $this->assertSameFiles($env, 'package-updated');
    }

    public function testPackageWithoutMetaDataFile_IsNotUpdated()
    {
        $env = $this->envSetup('package-synchronized', new Doubles\MockedFile(null));
        $this->command($env)->execute();
        $this->assertSameFiles($env, 'package-synchronized');
    }

    public function testDesynchronizedPackage_IsNotUpdated()
    {
        $env = $this->envSetup('package-desynchronized');
        $this->command($env)->execute();
        $this->assertSameFiles($env, 'package-desynchronized');
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
