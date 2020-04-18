<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command;

use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\Terminal;
use Shudd3r\PackageFiles\Properties;


class InitCommand implements Command
{
    private Properties $properties;
    private Subroutine $subroutine;
    private Terminal   $terminal;

    public function __construct(Properties $properties, Subroutine $subroutine, Terminal $terminal)
    {
        $this->properties = $properties;
        $this->subroutine = $subroutine;
        $this->terminal   = $terminal;
    }

    public function execute(): int
    {
        $this->subroutine->process($this->properties);

        return $this->terminal->exitCode();
    }
}
