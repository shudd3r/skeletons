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

use Shudd3r\Skeletons\Environment\Output;


class Messages
{
    private Output $output;
    private string $description;
    private int    $errorCode;
    private array  $status;

    public function __construct(Output $output, string $description, ?array $status = null, int $errorCode = 2)
    {
        $this->output      = $output;
        $this->description = $description;
        $this->status      = ($status ?? ['OK', 'FAIL']) + ['', ''];
        $this->errorCode   = $errorCode;
    }

    public function describeProcedure(): void
    {
        $postfix = $this->status[0] ? '...' : PHP_EOL;
        $this->output->send('- ' . $this->description . $postfix);
    }

    public function sendResult(bool $result): void
    {
        $message = $result ? $this->status[0] : $this->status[1];
        $this->output->send($message ? ' ' . $message . PHP_EOL : '', $result ? 0 : $this->errorCode);
    }
}
