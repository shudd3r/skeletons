<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\InitCommand;
use Shudd3r\PackageFiles\RuntimeEnv;


class FakeCommandFactory implements Factory
{
    public static $procedure;
    public static ?FakePropertiesReader $reader;

    public function command(RuntimeEnv $env): InitCommand
    {
        self::$reader = new FakePropertiesReader();
        return new InitCommand(self::$reader, new MockedSubroutine(self::$procedure));
    }
}
