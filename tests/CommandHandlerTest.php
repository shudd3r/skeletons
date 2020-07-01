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
use Shudd3r\PackageFiles\Tests\Doubles\FakePropertiesReader;


class CommandHandlerTest extends TestCase
{
    public function testInstantiation()
    {
        $command = new CommandHandler(new Doubles\FakePropertiesReader(), new Doubles\MockedSubroutine());
        $this->assertInstanceOf(CommandHandler::class, $command);
    }

    public function testPropertiesArePassedToSubroutine()
    {
        $properties = new Doubles\FakeProperties();
        $reader     = new FakePropertiesReader($properties);
        $subroutine = new Doubles\MockedSubroutine();
        $command    = new CommandHandler($reader, $subroutine);

        $command->execute();
        $this->assertSame($properties, $subroutine->passedProperties);
    }
}
