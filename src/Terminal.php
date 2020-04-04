<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;


class Terminal
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
    public function __construct($input, $output, $error)
    {
        $this->input  = $input;
        $this->output = $output;
        $this->error  = $error;
    }

    /**
     * Returns value provided from external stream resource (usually STDIN).
     *
     * @return string
     */
    public function input(): string
    {
        return trim(fgets($this->input));
    }

    /**
     * Sends message to external stream resource (usually STDOUT).
     *
     * @param string $message
     */
    public function display(string $message): void
    {
        fwrite($this->output, $message);
    }

    /**
     * Sends error message to error stream resource (usually STDERR)
     * and collects error code for exit result.
     *
     * @param string $message
     * @param int    $code
     */
    public function sendError(string $message, int $code): void
    {
        $this->errorCode |= $code;
        fwrite($this->error, $message);
    }

    /**
     * @return int Binary sum of error codes
     */
    public function exitCode(): int
    {
        return $this->errorCode;
    }
}
