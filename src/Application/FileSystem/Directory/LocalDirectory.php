<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem\Directory;

use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\File;


class LocalDirectory implements Directory
{
    private string $path;

    /**
     * @param string $path absolute directory path
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($this->normalizedPath($path), DIRECTORY_SEPARATOR);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return is_dir($this->path);
    }

    public function file(string $filename): File
    {
        $filename = trim($this->normalizedPath($filename), DIRECTORY_SEPARATOR);
        return new File\LocalFile($this->path . DIRECTORY_SEPARATOR . $filename);
    }

    private function normalizedPath(string $path): string
    {
        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }
}
