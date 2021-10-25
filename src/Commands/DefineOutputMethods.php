<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands;

use Shudd3r\PackageFiles\Commands\Command\DescribedCommand;
use Shudd3r\PackageFiles\Commands\Precondition\DescribedPrecondition;
use Shudd3r\PackageFiles\RuntimeEnv;


trait DefineOutputMethods
{
    private RuntimeEnv $env;

    private function commandInfo(string $message, Command $command): Command
    {
        return new DescribedCommand($command, $this->env->output(), $message);
    }

    private function checkInfo(string $message, Precondition $precondition, bool $status = true): Precondition
    {
        return new DescribedPrecondition($precondition, $this->env->output(), $message, $status);
    }
}
