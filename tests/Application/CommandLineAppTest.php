<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\CommandLineApp;
use Shudd3r\PackageFiles\Commands;
use Shudd3r\PackageFiles\Tests\Doubles;
use Shudd3r\PackageFiles\Tests\Doubles\FakeCommandFactory as Factory;
use Exception;


class CommandLineAppTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(CommandLineApp::class, $this->app());
    }

    public function testForExecutedCommand_RunMethod_ReturnsOutputErrorCode()
    {
        $app = $this->app($env);
        $this->assertSame(0, $app->run('command'));

        $env->terminal->errorCode = 123;
        $this->assertSame(123, $app->run('command'));
    }

    public function testForNotExistingCommand_RunMethod_SendsErrorToOutput()
    {
        $app = $this->app($env);
        $this->assertEmpty($env->terminal->messagesSent);

        $this->assertSame(1, $app->run('notCommand'));
        $this->assertNotEmpty($env->terminal->messagesSent);
    }

    public function testOptionsArePassedToCommand()
    {
        $this->app()->run('command', $options = ['foo' => 'bar']);
        $this->assertSame($options, Factory::$command->options);
    }

    public function testCommandIsExecuted()
    {
        $app = $this->app($env);
        Factory::$procedure = function () use ($env) { $env->terminal->send('executed'); };

        $this->assertSame([], $env->terminal->messagesSent);

        $app->run('command');
        $this->assertSame(['executed'], $env->terminal->messagesSent);
    }

    public function testUncheckedExceptionIsCaught()
    {
        $app = $this->app($env);
        Factory::$procedure = function () { throw new Exception('exc.message'); };

        $exitCode = $app->run('command');
        $this->assertSame(1, $exitCode);
        $this->assertSame(['exc.message'], $env->terminal->messagesSent);
    }

    private function app(Doubles\FakeRuntimeEnv &$env = null): CommandLineApp
    {
        $env ??= new Doubles\FakeRuntimeEnv();

        Factory::$procedure = null;
        Factory::$command   = null;

        return new CommandLineApp($env->output(), new Commands($env, ['command' => Factory::class]));
    }
}
