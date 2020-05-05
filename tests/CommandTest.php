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
use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\Properties;


class CommandTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(Command::class, $this->command($properties, $subroutine));
    }

    public function testPropertiesArePassedToSubroutine()
    {
        $this->command($properties, $subroutine)->execute();
        $this->assertSame($properties, $subroutine->propertiesPassed);
    }

    private function command(&$properties, &$subroutine): Command
    {
        $properties = new class() extends Properties {
            public function repositoryUrl(): string
            {
                return 'https://github.com/polymorphine/dev.git';
            }

            public function packageName(): string
            {
                return 'polymorphine/dev';
            }

            public function packageDescription(): string
            {
                return 'Package description';
            }

            public function sourceNamespace(): string
            {
                return 'Polymorphine\Dev';
            }
        };

        $subroutine = new class() implements Command\Subroutine {
            public $propertiesPassed;

            public function process(Properties $properties): void
            {
                $this->propertiesPassed = $properties;
            }
        };

        return new Command($properties, $subroutine);
    }
}
