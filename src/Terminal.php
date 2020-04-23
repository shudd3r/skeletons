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
     * Sends message to external stream resource with optional error code.
     *
     * @param string $message
     * @param int    $errorCode
     */
    public function render(string $message, int $errorCode = 0): void
    {
        $this->errorCode |= $errorCode;
        fwrite($errorCode === 0 ? $this->output : $this->error, $message);
    }

    /**
     * @return int Binary sum of error codes for rendered messages
     */
    public function exitCode(): int
    {
        return $this->errorCode;
    }
}
