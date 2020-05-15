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

use Shudd3r\PackageFiles\Command\Subroutine;


class Command
{
    private Properties $properties;
    private Subroutine $subroutine;

    public function __construct(Properties $properties, Subroutine $subroutine)
    {
        $this->properties = $properties;
        $this->subroutine = $subroutine;
    }

    public function execute(): void
    {
        $this->subroutine->process($this->properties);
    }
}
