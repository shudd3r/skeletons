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
use Shudd3r\PackageFiles\Commands;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\Routing;
use RuntimeException;


class CommandsTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(Routing::class, $this->commands());
    }

    public function testMissingCommand_ThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->commands(['foo' => Doubles\FakeCommandFactory::class])->command('bar');
    }

    public function testUnknownCommandFactoryClass_ThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->commands(['foo' => 'Unknown\\Factory\\ClassName'])->command('foo');
    }

    public function testCommandFactoryClass_ReturnsCommand()
    {
        $commands = $this->commands(['foo' => Doubles\FakeCommandFactory::class]);
        $this->assertInstanceOf(Command::class, $commands->command('foo'));
    }

    public function testRuntimeEnvDataIsPassedToFactory()
    {
        $this->commands(['foo' => Doubles\FakeCommandFactory::class], $env)->command('foo');
        $this->assertSame($env, Doubles\FakeCommandFactory::$env);
    }

    private function commands(array $factories = [], Doubles\FakeRuntimeEnv &$env = null): Commands
    {
        return new Commands($env = new Doubles\FakeRuntimeEnv(), $factories);
    }
}
