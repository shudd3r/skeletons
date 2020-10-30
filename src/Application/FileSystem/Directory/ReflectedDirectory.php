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


class ReflectedDirectory implements Directory
{
    private Directory $root;
    private Directory $origin;

    public function __construct(Directory $root, Directory $origin)
    {
        $this->root   = $root;
        $this->origin = $origin;
    }

    public function path(): string
    {
        return $this->root->path();
    }

    public function exists(): bool
    {
        return $this->root->exists();
    }

    public function subdirectory(string $name): Directory
    {
        return new self($this->root->subdirectory($name), $this->origin->subdirectory($name));
    }

    public function file(string $filename): File
    {
        return $this->root->file($filename);
    }

    public function files(): DirectoryFiles
    {
        return $this->origin->files()->reflectedIn($this->root);
    }
}
