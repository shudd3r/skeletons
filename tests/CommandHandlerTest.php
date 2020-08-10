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
use Shudd3r\PackageFiles\CommandHandler;
use Shudd3r\PackageFiles\Tests\Doubles\FakeToken;
use Shudd3r\PackageFiles\Tests\Doubles\MockedTerminal;
use Shudd3r\PackageFiles\Token\Reader;
use Exception;


class CommandHandlerTest extends TestCase
{
    public function testInstantiation()
    {
        $command = new CommandHandler(new Reader(new MockedTerminal()), new Doubles\MockedSubroutine());
        $this->assertInstanceOf(CommandHandler::class, $command);
    }

    public function testPropertiesArePassedToSubroutine()
    {
        $reader     = new Reader(new MockedTerminal());
        $subroutine = new Doubles\MockedSubroutine();
        $command    = new CommandHandler($reader, $subroutine);

        $command->execute();
        $this->assertEquals($reader->token(), $subroutine->passedToken);
    }

    public function testUnresolvedPropertiesStopExecution()
    {
        $token   = fn() => new FakeToken('value', 'exception message');
        $command = new CommandHandler(new Reader(new MockedTerminal(), $token), new Doubles\MockedSubroutine());

        $this->expectException(Exception::class);
        $command->execute();
    }
}
