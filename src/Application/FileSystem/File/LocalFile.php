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
use Shudd3r\PackageFiles\Application\FileSystem\AbstractNode;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class LocalFile extends AbstractNode implements File
{
    private Directory $rootDir;

    public function __construct(Directory $rootDir, string $path)
    {
        $this->rootDir = $rootDir;
        parent::__construct($this->expandedPath($rootDir, $path));
    }

    public function reflectedIn(Directory $rootDirectory): self
    {
        return new self($rootDirectory, $this->pathRelativeTo($this->rootDir));
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
