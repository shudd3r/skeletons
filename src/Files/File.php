<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Files;

use Shudd3r\PackageFiles\Files;


class File
{
    private string $filename;
    private Files  $files;

    public function __construct(string $filename, Files $files)
    {
        $this->filename = $filename;
        $this->files    = $files;
    }

    public function name(): string
    {
        return $this->filename;
    }

    public function exists(): bool
    {
        return $this->files->exists($this->filename);
    }

    public function contents(): string
    {
        return $this->files->contents($this->filename);
    }

    public function write(string $contents): void
    {
        $this->files->write($this->filename, $contents);
    }
}
