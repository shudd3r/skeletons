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

    abstract public function exists(): bool;

    protected function normalizedPath(string $path): string
    {
        return rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR);
    }
}
