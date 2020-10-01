<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem;


abstract class AbstractNode implements Node
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $this->normalizedPath($path);
    }

    public function path(): string
    {
        return $this->path;
    }

    public function pathRelativeTo(Directory $ancestorDirectory): string
    {
        $ancestorPath = $ancestorDirectory->path();
        if (strpos($this->path, $ancestorPath) !== 0) {
            throw new Exception\InvalidAncestorDirectory();
        }

        return substr($this->path, strlen($ancestorPath) + 1);
    }

    abstract public function exists(): bool;

    protected function normalizedPath(string $path): string
    {
        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }

    protected function expandedPath(Directory $rootDir, string $postfix): string
    {
        return $rootDir->path() . DIRECTORY_SEPARATOR . ltrim($postfix, '\\/');
    }
}
