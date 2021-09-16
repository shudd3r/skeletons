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
        $env = $this->envSetup('package');
        $this->command($env)->execute();
        $this->assertSameFiles($env, 'package-initialized');
    }

    public function testExistingMetaDataFile_AbortsExecutionWithoutSideEffects()
    {
        $env = $this->envSetup('package', new Doubles\MockedFile());
        $this->command($env)->execute();
        $this->assertSameFiles($env, 'package');
    }

    public function testOverwritingBackupFile_AbortsExecutionWithoutSideEffects()
    {
        $env = $this->envSetup('package', null, true);
        $this->command($env)->execute();
        $this->assertSameFiles($env, 'package');
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
