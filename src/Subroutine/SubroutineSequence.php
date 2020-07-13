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


class SubroutineSequence implements Subroutine
{
    /** @var Subroutine[] */
    private array $subroutines;

    public function __construct(Subroutine ...$subroutines)
    {
        $this->subroutines = $subroutines;
    }

    public function process(Token $token): void
    {
        foreach ($this->subroutines as $subroutine) {
            $subroutine->process($token);
        }
    }
}
