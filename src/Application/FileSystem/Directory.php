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

use Shudd3r\PackageFiles\Files\File;


interface Directory
{
    /**
     * @return string Absolute path to directory
     */
    public function path(): string;

    /**
     * @param string $filename
     *
     * @return File
     */
    public function file(string $filename): File;

    /**
     * @param File $file
     */
    public function save(File $file): void;
}
