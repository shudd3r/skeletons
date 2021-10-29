<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Processors\Processor;

use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Environment\Output;
use Shudd3r\Skeletons\Replacements\Token;


class DescribedProcessor implements Processor
{
    private Processor $processor;
    private Output    $output;
    private string    $message;
    private bool      $withStatus;

    public function __construct(Processor $processor, Output $output, string $message, bool $status = false)
    {
        $this->processor  = $processor;
        $this->output     = $output;
        $this->message    = $message;
        $this->withStatus = $status;
    }

    public function process(Token $token): bool
    {
        $postfix = $this->withStatus ? '' : PHP_EOL;
        $this->output->send('    ' . $this->message . $postfix);

        $status = $this->processor->process($token);

        if ($this->withStatus) {
            $message = $status ? '... OK' : '... FAIL';
            $this->output->send($message . PHP_EOL, $status ? 0 : 1);
        }

        return $status;
    }
}
