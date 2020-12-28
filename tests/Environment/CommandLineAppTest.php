<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Environment;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Environment\CommandLineApp;
use Shudd3r\PackageFiles\Tests\Doubles;
use Exception;


class CommandLineAppTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(CommandLineApp::class, $this->app());
    }

    public function testForExecutedCommand_RunMethod_ReturnsOutputErrorCode()
    {
        $app = $this->app($output);
        $this->assertSame(0, $app->run(Doubles\FakeRouting::VALID_COMMAND));

        $output->errorCode = 123;
        $this->assertSame(123, $app->run(Doubles\FakeRouting::VALID_COMMAND));
    }

    public function testRoutingExceptionMessageIsSentToOutput()
    {
        $app = $this->app($output);
        $this->assertEmpty($output->messagesSent);

        $this->assertSame(1, $app->run('notCommand'));
        $this->assertSame([Doubles\FakeRouting::EXCEPTION_MESSAGE], $output->messagesSent);
    }

    public function testOptionsArePassedToRouting()
    {
        $app = $this->app($output, $routing);

        $app->run('command', $options = ['foo' => 'bar']);
        $this->assertSame($options, $routing->options);
    }

    public function testUncheckedExceptionIsCaught()
    {
        $command = new Doubles\FakeCommand(function () { throw new Exception('exc.message'); });
        $routing = new Doubles\FakeRouting($command);
        $app     = $this->app($output, $routing);

        $exitCode = $app->run('command');
        $this->assertSame(1, $exitCode);
        $this->assertSame(['exc.message'], $output->messagesSent);
    }

    private function app(Doubles\MockedTerminal &$output = null, Doubles\FakeRouting &$routing = null): CommandLineApp
    {
        return new CommandLineApp($output = new Doubles\MockedTerminal(), $routing ??= new Doubles\FakeRouting());
    }
}
