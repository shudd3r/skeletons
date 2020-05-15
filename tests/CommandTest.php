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
use Shudd3r\PackageFiles\Command;


class CommandTest extends TestCase
{
    public function testInstantiation()
    {
        $command = new Command(new Doubles\FakeProperties(), new Doubles\MockedSubroutine());
        $this->assertInstanceOf(Command::class, $command);
    }

    public function testPropertiesArePassedToSubroutine()
    {
        $properties = new Doubles\FakeProperties();
        $subroutine = new Doubles\MockedSubroutine();
        $command    = new Command($properties, $subroutine);

        $command->execute();
        $this->assertSame($properties, $subroutine->passedProperties);
    }
}
