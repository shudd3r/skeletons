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
    private $buildProperties;
    private Subroutine $subroutine;
    private Terminal   $terminal;

    public function __construct(callable $buildProperties, Subroutine $subroutine, Terminal $terminal)
    {
        $this->buildProperties = $buildProperties;
        $this->subroutine      = $subroutine;
        $this->terminal        = $terminal;
    }

    public function execute(array $options): int
    {
        $this->subroutine->process($this->properties($options));

        return $this->terminal->exitCode();
    }

    private function properties(array $options): Properties
    {
        return ($this->buildProperties)($options);
    }
}
