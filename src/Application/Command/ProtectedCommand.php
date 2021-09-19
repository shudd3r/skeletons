<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Command;

use Shudd3r\PackageFiles\Application\Command;


class ProtectedCommand implements Command
{
    private Command      $command;
    private Precondition $precondition;

    public function __construct(Command $command, Precondition $precondition)
    {
        $this->command      = $command;
        $this->precondition = $precondition;
    }

    public function execute(): void
    {
        if ($this->precondition->isFulfilled()) { $this->command->execute(); }
    }
}
