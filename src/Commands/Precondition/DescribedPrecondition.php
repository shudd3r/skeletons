<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Precondition;

use Shudd3r\Skeletons\Commands\Precondition;
use Shudd3r\Skeletons\Environment\Output;


class DescribedPrecondition implements Precondition
{
    private Precondition $precondition;
    private Output       $output;
    private string       $description;
    private array        $status;

    public function __construct(Precondition $precondition, Output $output, string $description, ?array $status = null)
    {
        $this->precondition = $precondition;
        $this->output       = $output;
        $this->description  = $description;
        $this->status       = ($status ?? ['OK', 'FAIL']) + ['', ''];
    }

    public function isFulfilled(): bool
    {
        $postfix = $this->status[0] ? '...' : PHP_EOL;
        $this->output->send('- ' . $this->description . $postfix);
        $isFulfilled = $this->precondition->isFulfilled();

        $message = $isFulfilled ? $this->status[0] : $this->status[1];
        $this->output->send($message ? ' ' . $message . PHP_EOL : '', $isFulfilled ? 0 : 1);

        return $isFulfilled;
    }
}
