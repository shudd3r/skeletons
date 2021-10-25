<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands\Command;

use Shudd3r\PackageFiles\Commands\Command;
use Shudd3r\PackageFiles\Environment\Output;


class DescribedCommand implements Command
{
    private Command $command;
    private Output  $output;
    private string  $message;

    public function __construct(Command $command, Output $output, string $message)
    {
        $this->command = $command;
        $this->output  = $output;
        $this->message = $message;
    }

    public function execute(): void
    {
        $this->output->send('- ' . $this->message . PHP_EOL);
        $this->command->execute();
    }
}
