<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem\Local;

use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryFiles;
use Shudd3r\PackageFiles\Application\FileSystem\File;


class LocalDirectory implements Directory
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return is_dir($this->path);
    }

    public function subdirectory(string $name): self
    {
        return new self($this->path . DIRECTORY_SEPARATOR . ltrim($name, '\\/'));
    }

    public function file(string $filename): File
    {
        return new LocalFile($this, $filename);
    }

    public function files(): DirectoryFiles
    {
        if (!$this->exists()) { return new DirectoryFiles([]); }

        return new DirectoryFiles($this->readDirectory());
    }

    private function readDirectory(string $subdirectory = ''): array
    {
        $absolutePath = $subdirectory ? $this->path . DIRECTORY_SEPARATOR . $subdirectory : $this->path;
        $names        = array_diff(scandir($absolutePath), ['.', '..']);

        $files       = [];
        $directories = [];
        foreach ($names as $name) {
            $relativePath = $subdirectory ? $subdirectory . DIRECTORY_SEPARATOR . $name : $name;
            $filename     = $absolutePath . DIRECTORY_SEPARATOR . $name;
            if (is_file($filename)) {
                $files[] = $this->file($relativePath);
            } elseif (is_dir($filename)) {
                $directories[] = $relativePath;
            }
        }

        foreach ($directories as $directory) {
            $files = array_merge($files, $this->readDirectory($directory));
        }

        return $files;
    }
}
