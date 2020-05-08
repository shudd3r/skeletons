<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\FileSystem\File as FileInterface;


class MockedFile implements FileInterface
{
    public string $contents;
    public bool   $exists;
    public string $path;

    public function __construct(string $contents = '', bool $exists = true, string $path = __DIR__)
    {
        $this->contents = $contents;
        $this->exists   = $exists;
        $this->path     = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function contents(): string
    {
        return $this->contents;
    }

    public function write(string $contents): void
    {
        $this->contents = $contents;
    }
}
