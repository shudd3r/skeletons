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
use Shudd3r\PackageFiles\Properties\Reader;


class CommandHandlerTest extends TestCase
{
    public function testInstantiation()
    {
        $reader  = new Reader(new Doubles\FakeSource(), new Doubles\MockedTerminal());
        $command = new CommandHandler($reader, new Doubles\MockedSubroutine());
        $this->assertInstanceOf(CommandHandler::class, $command);
    }

    public function testPropertiesArePassedToSubroutine()
    {
        $properties = new Doubles\FakeSource(['repositoryName' => 'foo/bar']);
        $reader     = new Reader($properties, new Doubles\MockedTerminal());
        $subroutine = new Doubles\MockedSubroutine();
        $command    = new CommandHandler($reader, $subroutine);

        $command->execute();
        $this->assertEquals($reader->properties(), $subroutine->passedProperties);
    }

    public function testUnresolvedPropertiesStopExecution()
    {
        $properties = new Doubles\FakeSource();
        $output     = new Doubles\MockedTerminal();
        $reader     = new Reader($properties, $output);
        $subroutine = new Doubles\MockedSubroutine();
        $command    = new CommandHandler($reader, $subroutine);

        $output->errorCode = 1;

        $command->execute();
        $this->assertNull($subroutine->passedProperties);
    }
}
