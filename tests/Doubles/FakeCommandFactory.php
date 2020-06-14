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

use Shudd3r\PackageFiles\Command;


class FakeCommandFactory extends Command\Factory
{
    public static $procedure;
    public static ?FakePropertiesReader $reader;

    public function command(): Command
    {
        self::$reader = new FakePropertiesReader();
        return new Command(self::$reader, new MockedSubroutine(self::$procedure));
    }
}
