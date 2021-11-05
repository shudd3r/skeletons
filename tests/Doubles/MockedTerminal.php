<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Environment\Terminal;


class MockedTerminal extends Terminal
{
    private array $inputStrings = [];
    private array $messagesSent = [];
    private int   $errorCode    = 0;

    public function value(string $prompt = ''): string
    {
        if ($prompt) {
            $this->messagesSent[] = $prompt;
        }
        $first = array_shift($this->inputStrings);
        return $first ?: '';
    }

    public function send(string $message, int $errorCode = 0): void
    {
        $this->errorCode |= $errorCode;
        $this->messagesSent[] = $message;
    }

    public function exitCode(): int
    {
        return $this->errorCode;
    }

    public function addInput(string $input): void
    {
        $this->inputStrings[] = $input;
    }

    public function messagesSent(): array
    {
        $messages = $this->messagesSent;
        $this->messagesSent = [];
        return $messages;
    }

    public function reset(): self
    {
        $this->messagesSent = [];
        $this->inputStrings = [];
        $this->errorCode    = 0;
        return $this;
    }
}
