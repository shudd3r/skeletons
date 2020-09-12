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
use Shudd3r\PackageFiles\Application\FileSystem\PathNormalizationMethods;
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryStructureMethods;


class LocalFile implements File
{
    use PathNormalizationMethods;
    use DirectoryStructureMethods;

    private string $path;

    /**
     * @param string $path absolute file path
     */
    public function __construct(string $path)
    {
        $this->path = $this->normalizedPath($path);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function pathRelativeTo(Directory $directory): string
    {
        $parentPath = $directory->path();
        if (strpos($this->path, $parentPath) !== 0) {
            return $this->path;
        }

        return substr($this->path, strlen($parentPath) + 1);
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
            $this->createDirectoryStructure(dirname($this->path));
        }

        file_put_contents($this->path, $contents);
    }
}
