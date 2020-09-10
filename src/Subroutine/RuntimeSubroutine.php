<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Subroutine;

use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Token;


class RuntimeSubroutine implements Subroutine
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function process(Token $token): void
    {
        $this->factory->subroutine()->process($token);
    }
}
