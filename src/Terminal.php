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

    /**
     * @param resource $input
     * @param resource $output
     */
    public function __construct($input, $output)
    {
        $this->input  = $input;
        $this->output = $output;
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
}
