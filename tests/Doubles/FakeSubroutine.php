<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\Properties;


class FakeSubroutine implements Subroutine
{
    private $procedure;

    /**
     * @param callable|null $procedure fn() => void
     */
    public function __construct(?callable $procedure = null)
    {
        $this->procedure = $procedure;
    }

    public function process(Properties $properties): void
    {
        if (!$this->procedure) { return; }
        ($this->procedure)();
    }
}
