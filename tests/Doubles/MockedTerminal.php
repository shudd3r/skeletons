<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Application\Input;


class MockedTerminal implements Input, Output
{
    public $inputStrings = [];
    public $messagesSent = [];
    public $errorCode    = 0;

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
}
