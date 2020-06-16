<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Properties\Reader;
use Shudd3r\PackageFiles\Command\Subroutine;


class InitCommand implements Command
{
    private Reader     $reader;
    private Subroutine $subroutine;

    public function __construct(Reader $reader, Subroutine $subroutine)
    {
        $this->reader     = $reader;
        $this->subroutine = $subroutine;
    }

    public function execute(array $options): void
    {
        $this->subroutine->process($this->reader->properties($options));
    }
}
