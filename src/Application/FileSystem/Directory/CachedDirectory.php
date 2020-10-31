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
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryFiles;
use Shudd3r\PackageFiles\Application\FileSystem\File;


class CachedDirectory implements Directory
{
    private Directory       $origin;
    private ?DirectoryFiles $files;

    public function __construct(Directory $origin)
    {
        $this->origin = $origin;
    }

    public function path(): string
    {
        return $this->origin->path();
    }

    public function exists(): bool
    {
        return $this->origin->exists();
    }

    public function subdirectory(string $name): Directory
    {
        return new self($this->origin->subdirectory($name));
    }

    public function file(string $filename): File
    {
        return $this->origin->file($filename);
    }

    public function files(): DirectoryFiles
    {
        return $this->files ??= $this->origin->files();
    }
}
