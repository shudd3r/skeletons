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

use Shudd3r\PackageFiles\Validate;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Command\Precondition;
use Shudd3r\PackageFiles\Application\Token\TokenCache;


class ValidateTest extends IntegrationTestCase
{
    public function testInitializedPackage_IsValid()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-initialized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->command($env)->execute();

        $this->assertSame(0, $env->output()->exitCode());
        $this->assertTrue($this->validatePrecondition($env)->isFulfilled());
    }

    public function testSynchronizedPackage_IsValid()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-synchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->command($env)->execute();

        $this->assertSame(0, $env->output()->exitCode());
        $this->assertTrue($this->validatePrecondition($env)->isFulfilled());
    }

    public function testDesynchronizedPackage_IsInvalid()
    {
        $files   = new Fixtures\ExampleFiles('example-files');
        $package = $files->directory('package-unsynchronized');
        $env     = $this->envSetup($package, $files->directory('template'));

        $this->command($env)->execute();

        $this->assertNotEquals(0, $env->output()->exitCode());
        $this->assertFalse($this->validatePrecondition($env)->isFulfilled());
    }

    //todo: failed precondition tests

    protected function command(RuntimeEnv $env): Command
    {
        $validate = new Validate($env);
        return $validate->command([]);
    }

    private function validatePrecondition(RuntimeEnv $env): Precondition
    {
        $validate = new Validate($env);
        return $validate->synchronizedSkeleton(new TokenCache());
    }
}
