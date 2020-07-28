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
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Subroutine;


class FakeCommandFactory extends Factory
{
    public static ?array $options = null;

    protected function tokenReader(array $options): Reader
    {
        self::$options = $options;
        return new FakeReader();
    }

    protected function subroutine(array $options): Subroutine
    {
        return new MockedSubroutine();
    }
}
