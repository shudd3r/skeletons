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

use Shudd3r\PackageFiles\Factory;
use Shudd3r\PackageFiles\Application\Command;


class FakeCommandFactory extends Factory
{
    public static $procedure;
    public static ?FakeCommand $command;
    public static ?array       $options;

    public function command(array $options): Command
    {
        self::$options = $options;
        return self::$command = new FakeCommand(self::$procedure);
    }
}
