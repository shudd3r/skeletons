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
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryStructureMethods;
use Shudd3r\PackageFiles\Application\FileSystem\File;


class LocalDirectory implements Directory
{
    use DirectoryStructureMethods;

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

    public function create(): void
    {
        if ($this->exists()) { return; }
        $this->createDirectoryStructure($this->path);
    }

    public function file(string $filename): File
    {
        $filename = trim($this->normalizedPath($filename), DIRECTORY_SEPARATOR);
        return new File\LocalFile($this->path . DIRECTORY_SEPARATOR . $filename);
    }

    public function subdirectory(string $name): Directory
    {
        $name = trim($this->normalizedPath($name), DIRECTORY_SEPARATOR);
        return new self($this->path . DIRECTORY_SEPARATOR . $name);
    }

    public function files(): array
    {
        if (!$this->exists()) { return []; }

        $map    = fn($filename) => $this->file($filename);
        $filter = fn(File $file) => $file->exists();

        return $this->nodes($map, $filter);
    }

    public function subdirectories(): array
    {
        if (!$this->exists()) { return []; }

        $map    = fn($name) => $this->subdirectory($name);
        $filter = fn(Directory $directory) => $directory->exists();

        return $this->nodes($map, $filter);
    }

    private function normalizedPath(string $path): string
    {
        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }

    private function nodes(callable $map, callable $filter): array
    {
        $names = array_diff(scandir($this->path), ['.', '..']);
        $nodes = array_map($map, $names);
        return array_values(array_filter($nodes, $filter));
    }
}
