<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Command;

use Shudd3r\Skeletons\Commands\Command;
use Shudd3r\Skeletons\Environment\Output;


class DisplayMessage implements Command
{
    private string $message;
    private Output $output;

    public function __construct(string $message, Output $output)
    {
        $this->output  = $output;
        $this->message = $message;
    }

    public function execute(): void
    {
        $this->output->send($this->message);
    }
}
