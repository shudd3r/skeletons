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

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Validate;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\Command\Precondition;
use Shudd3r\PackageFiles\Application\Token\TokenCache;


class ValidateTest extends TestCase
{
    public function testFactoryCreatesCommand()
    {
        $factory = new Validate(new Doubles\FakeRuntimeEnv());
        $this->assertInstanceOf(Command::class, $factory->command([]));
    }

    public function testFactoryCanCreatePrecondition()
    {
        $factory = new Validate(new Doubles\FakeRuntimeEnv());
        $this->assertInstanceOf(Precondition::class, $factory->synchronizedSkeleton(new TokenCache()));
    }

    public function testMissingMetaDataFile_StopsExecution()
    {
        $setup = new TestEnvSetup();

        $factory = new Validate($setup->env);
        $factory->command([])->execute();

        $this->assertSame([], $setup->env->output()->messagesSent);
    }

    public function testInvalidMetaData_StopsExecution()
    {
        $setup = new TestEnvSetup();
        $setup->addMetaData(['namespace.src' => 'Not/A/Namespace']);

        $factory = new Validate($setup->env);
        $factory->command([])->execute();

        $this->assertSame([], $setup->env->output()->messagesSent);
    }

    public function testMatchingFiles_OutputsNoErrorCode()
    {
        $setup = new TestEnvSetup();
        $setup->addMetaData();
        $setup->addComposer();
        $setup->addGeneratedFile();

        $factory = new Validate($setup->env);
        $factory->command([])->execute();

        $this->assertSame(0, $setup->env->output()->exitCode());
        $this->assertTrue($factory->synchronizedSkeleton(new TokenCache())->isFulfilled());
    }

    public function testNotMatchingFiles_OutputsErrorCode()
    {
        $setup = new TestEnvSetup();
        $setup->addMetaData(['repository.name' => 'another/repo']);
        $setup->addComposer();
        $setup->addGeneratedFile();

        $factory = new Validate($setup->env);
        $factory->command([])->execute();

        $this->assertSame(1, $setup->env->output()->exitCode());
        $this->assertFalse($factory->synchronizedSkeleton(new TokenCache())->isFulfilled());
    }
}
