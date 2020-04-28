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

use Shudd3r\PackageFiles\Files\File;
use Shudd3r\PackageFiles\Files\Exception;


/**
 * Files within concrete directory.
 * All filename parameters should be relative to this directory.
 */
interface Files
{
    /**
     * @return string Absolute path to directory in which files are located
     */
    public function directory(): string;

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function exists(string $filename): bool;

    /**
     * @param string $filename
     *
     * @return File
     */
    public function file(string $filename): File;

    /**
     * @param string $filename
     *
     * @throws Exception\FileNotFoundException
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
