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

use Shudd3r\PackageFiles\Application;


class ValidateTest extends IntegrationTestCase
{
    public function testInitializedPackage_IsValid()
    {
        $env = $this->envSetup('package-initialized');
        $app = new Application($env);

        $app->run('check');
        $this->assertSame(0, $env->output()->exitCode());
    }

    public function testSynchronizedPackage_IsValid()
    {
        $env = $this->envSetup('package-synchronized');
        $app = new Application($env);

        $app->run('check');
        $this->assertSame(0, $env->output()->exitCode());
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $env = $this->envSetup('package-desynchronized');
        $app = new Application($env);

        $app->run('check');
        $this->assertNotEquals(0, $env->output()->exitCode());
    }

    //todo: failed precondition tests
}
