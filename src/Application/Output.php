<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application;


interface Output
{
    /**
     * Sends message to external stream resource with optional error code.
     *
     * @param string $message
     * @param int    $errorCode
     */
    public function send(string $message, int $errorCode = 0): void;

    /**
     * @return int Binary sum of error codes for rendered messages
     */
    public function exitCode(): int;
}
