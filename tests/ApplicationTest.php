<?php

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
use Shudd3r\PackageFiles\Application;
use Shudd3r\PackageFiles\Command\Routing;
use Shudd3r\PackageFiles\RuntimeEnv;
use Exception;


class ApplicationTest extends TestCase
{
    protected function tearDown(): void
    {
        Doubles\MockedFactory::$procedure = null;
        Doubles\MockedFactory::$passedOptions = null;
    }

    public function testInstantiation()
    {
        $this->assertInstanceOf(Application::class, $this->app());
    }

    public function testForExecutedCommand_RunMethod_ReturnsOutputErrorCode()
    {
        $app = $this->app($output);
        $this->assertSame(0, $app->run('command'));

        $output->errorCode = 123;
        $this->assertSame(123, $app->run('command'));
    }

    public function testForNotExistingCommand_RunMethod_SendsErrorToOutput()
    {
        $app = $this->app($terminal);
        $this->assertEmpty($terminal->messagesSent);

        $this->assertSame(1, $app->run('notCommand'));
        $this->assertNotEmpty($terminal->messagesSent);
    }

    public function testOptionsArePassedToCommandFactory()
    {
        $this->app()->run('command', $options = ['foo' => 'bar']);
        $this->assertSame($options, Doubles\MockedFactory::$passedOptions);
    }

    public function testCommandIsExecuted()
    {
        $app = $this->app($output);
        Doubles\MockedFactory::$procedure = function () use ($output) { $output->send('executed'); };

        $this->assertSame([], $output->messagesSent);

        $app->run('command');
        $this->assertSame(['executed'], $output->messagesSent);
    }

    public function testUncheckedExceptionIsCaught()
    {
        Doubles\MockedFactory::$procedure = function () {
            throw new Exception('exc.message');
        };

        $exitCode = $this->app($output)->run('command');
        $this->assertSame(1, $exitCode);
        $this->assertSame(['exc.message'], $output->messagesSent);
    }

    private function app(Doubles\MockedTerminal &$terminal = null): Application
    {
        $terminal ??= new Doubles\MockedTerminal();

        $directory = new Doubles\FakeDirectory();
        $env       = new RuntimeEnv($terminal, $terminal, $directory, $directory);

        return new Application($terminal, new Routing($env, ['command' => Doubles\MockedFactory::class]));
    }
}