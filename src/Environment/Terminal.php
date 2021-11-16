<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment;


class Terminal implements Input, Output
{
    private $input;
    private $output;
    private $error;

    private int $errorCode = 0;

    /**
     * @param resource $input
     * @param resource $output
     * @param resource $error
     */
    public function __construct($input = null, $output = null, $error = null)
    {
        $this->input  = $input ?? STDIN;
        $this->output = $output ?? STDOUT;
        $this->error  = $error ?? STDERR;
    }

    public function value(string $prompt = ''): string
    {
        if ($prompt) { $this->send($prompt); }
        return trim(fgets($this->input));
    }

    public function send(string $message, int $errorCode = 0): void
    {
        $this->errorCode |= $errorCode;
        if (!$message) { return; }
        fwrite($errorCode === 0 ? $this->output : $this->error, $message);
    }

    public function exitCode(): int
    {
        return $this->errorCode;
    }
}
