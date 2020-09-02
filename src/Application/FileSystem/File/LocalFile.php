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


class LocalFile implements File
{
    private string $path;
    private string $contents;

    /**
     * @param string $path absolute file path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return is_file($this->path);
    }

    public function contents(): string
    {
        return $this->contents ??= $this->exists() ? file_get_contents($this->path) : '';
    }

    public function write(string $contents): void
    {
        if (!$this->exists()) {
            $this->createMissingDirectories(dirname($this->path));
        }

        $this->contents = $contents;
        file_put_contents($this->path, $this->contents);
    }

    private function createMissingDirectories(string $path): void
    {
        $missingDir = [];
        while (!is_dir($path)) {
            $missingDir[] = basename($path);
            $path = dirname($path);
        }

        while ($dir = array_pop($missingDir)) {
            mkdir($path .= DIRECTORY_SEPARATOR . $dir);
        }
    }
}
