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


interface Directory extends Node
{
    public function create(): void;

    /**
     * @param string $filename file basename or relative file path
     *
     * @return File
     */
    public function file(string $filename): File;

    /**
     * @param string $name directory name or relative directory path
     *
     * @return Directory
     */
    public function subdirectory(string $name): Directory;

    public function files(): DirectoryFiles;
}
