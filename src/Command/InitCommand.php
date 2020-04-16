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
use Shudd3r\PackageFiles\Files;
use Shudd3r\PackageFiles\Terminal;
use Shudd3r\PackageFiles\Properties;


class InitCommand implements Command
{
    private Subroutine $subroutine;
    private Files      $packageFiles;
    private Terminal   $terminal;

    public function __construct(Subroutine $subroutine, Files $packageFiles, Terminal $terminal)
    {
        $this->subroutine   = $subroutine;
        $this->packageFiles = $packageFiles;
        $this->terminal     = $terminal;
    }

    public function execute(array $options): int
    {
        $properties = new Properties\FileReadProperties($this->packageFiles);
        $properties = new Properties\PredefinedProperties($options, $properties);
        $properties = new Properties\ResolvedProperties($properties, $this->packageFiles);
        if (isset($options['i']) || isset($options['interactive'])) {
            $properties = new Properties\InputProperties($this->terminal, $properties);
        }
        $properties = new Properties\CachedProperties($properties);

        $this->subroutine->process($properties);

        return $this->terminal->exitCode();
    }
}
