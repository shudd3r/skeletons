<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem;


interface File
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return bool
     */
    public function exists(): bool;

    /**
     * @return string
     */
    public function contents(): string;

    /**
     * @param string $contents
     */
    public function write(string $contents): void;
}
