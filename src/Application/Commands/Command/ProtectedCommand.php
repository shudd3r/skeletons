<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Commands\Command;

use Shudd3r\PackageFiles\Application\Commands\Command;
use Shudd3r\PackageFiles\Application\Commands\Precondition;
use Shudd3r\PackageFiles\Environment\Output;


class ProtectedCommand implements Command
{
    private Command      $command;
    private Precondition $precondition;
    private Output       $output;

    public function __construct(Command $command, Precondition $precondition, Output $output)
    {
        $this->command      = $command;
        $this->precondition = $precondition;
        $this->output       = $output;
    }

    public function execute(): void
    {
        if (!$this->precondition->isFulfilled()) {
            $this->output->send('Precondition failed', 1);
            return;
        }

        $this->command->execute();
    }
}
