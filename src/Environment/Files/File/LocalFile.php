<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\Files\File;

use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\Paths;


class LocalFile implements File
{
    use Paths;

    private string $name;
    private string $path;

    public function __construct(Directory $rootDir, string $name)
    {
        $this->name = $this->normalized($name);
        $this->path = $rootDir->path() . DIRECTORY_SEPARATOR . $this->normalized($name, DIRECTORY_SEPARATOR);
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
