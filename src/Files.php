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


interface Files
{
    /**
     * @param string $filename
     *
     * @return bool
     */
    public function exists(string $filename): bool;

    /**
     * @param string $filename
     *
     * @return string
     */
    public function contents(string $filename): string;

    /**
     * @param string $filename
     * @param string $contents
     */
    public function write(string $filename, string $contents): void;
}
