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

use Shudd3r\PackageFiles\Application\FileSystem\AbstractNode;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryFiles;
use Shudd3r\PackageFiles\Application\FileSystem\File;


class LocalDirectory extends AbstractNode implements Directory
{
    public function exists(): bool
    {
        return is_dir($this->path);
    }

    public function file(string $filename): File
    {
        return new File\LocalFile($this, $filename);
    }

    public function subdirectory(string $name): Directory
    {
        return new self($this->expandedPath($this, $name));
    }

    public function files(): DirectoryFiles
    {
        if (!$this->exists()) { return new DirectoryFiles([]); }

        return new DirectoryFiles($this->readDirectory());
    }

    private function readDirectory(): array
    {
        $map    = fn($filename) => $this->file($filename);
        $filter = fn(File $file) => $file->exists();
        $files  = $this->nodes($map, $filter);

        foreach ($this->subdirectories() as $subdirectory) {
            foreach ($subdirectory->files()->toArray() as $file) {
                $files[] = $this->file($file->pathRelativeTo($this));
            }
        }

        return $files;
    }

    private function subdirectories(): array
    {
        if (!$this->exists()) { return []; }

        $map    = fn($name) => $this->subdirectory($name);
        $filter = fn(Directory $directory) => $directory->exists();

        return $this->nodes($map, $filter);
    }

    private function nodes(callable $map, callable $filter): array
    {
        $names = array_diff(scandir($this->path), ['.', '..']);
        $nodes = array_map($map, $names);
        return array_values(array_filter($nodes, $filter));
    }
}
