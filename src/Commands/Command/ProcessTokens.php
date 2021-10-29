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
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Environment\Output;


class ProcessTokens implements Command
{
    private Reader    $reader;
    private Processor $processor;
    private Output    $output;

    public function __construct(Reader $reader, Processor $processor, Output $output)
    {
        $this->reader    = $reader;
        $this->processor = $processor;
        $this->output    = $output;
    }

    public function execute(): void
    {
        if (!$token = $this->reader->token()) { return; }

        if (!$this->processor->process($token)) {
            $this->output->send('Processing FAILED', 1);
        }
    }
}
