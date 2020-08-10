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
use Shudd3r\PackageFiles\Subroutine;


class FakeCommandFactory extends Factory
{
    public static array $optionsField = [];

    protected function tokenCallbacks(): array
    {
        self::$optionsField = $this->options;
        return [];
    }

    protected function subroutine(): Subroutine
    {
        return new MockedSubroutine();
    }
}
