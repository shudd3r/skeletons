<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem\File;

use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class LocalFile implements File
{
    private string $name;
    private string $path;

    public function __construct(Directory $rootDir, string $name)
    {
        $this->name = trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name), DIRECTORY_SEPARATOR);
        $this->path = $rootDir->path() . DIRECTORY_SEPARATOR . $this->name;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function exists(): bool
    {
        return is_file($this->path);
    }

    public function contents(): string
    {
        return $this->exists() ? file_get_contents($this->path) : '';
    }

    public function write(string $contents): void
    {
        if (!$this->exists()) {
            $this->createDirectoryStructure();
        }

        file_put_contents($this->path, $contents);
    }

    private function createDirectoryStructure(): void
    {
        $path = dirname($this->path);
        if (is_dir($path)) { return; }

        $missingDir = [];
        while (!is_dir($path)) {
            $missingDir[] = basename($path);
            $path = dirname($path);
        }

        foreach (array_reverse($missingDir) as $directory) {
            mkdir($path .= DIRECTORY_SEPARATOR . $directory);
        }
    }
}
