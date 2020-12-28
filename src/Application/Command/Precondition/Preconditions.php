<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Command\Precondition;

use Shudd3r\PackageFiles\Application\Command\Precondition;


class Preconditions implements Precondition
{
    private array $preconditions;

    public function __construct(Precondition ...$preconditions)
    {
        $this->preconditions = $preconditions;
    }

    public function isFulfilled(): bool
    {
        foreach ($this->preconditions as $precondition) {
            if ($precondition->isFulfilled()) { continue; }
            return false;
        }

        return true;
    }
}
