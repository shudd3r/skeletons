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


/**
 * Port separating application from Input/Output side-effects.
 */
interface Terminal
{
    /**
     * Returns value provided from external source (usually STDIN).
     *
     * @return string
     */
    public function input(): string;

    /**
     * Sends message to external device (usually STDOUT).
     *
     * @param string $message
     */
    public function send(string $message): void;
}
