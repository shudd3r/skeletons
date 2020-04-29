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

use Shudd3r\PackageFiles\Application\FileSystem\File as FileInterface;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class File implements FileInterface
{
    private string    $filename;
    private Directory $directory;
    private string    $contents;

    public function __construct(string $filename, Directory $directory)
    {
        $this->filename  = $filename;
        $this->directory = $directory;
    }

    public function name(): string
    {
        return $this->filename;
    }

    public function exists(): bool
    {
        return file_exists($this->path());
    }

    public function contents(): string
    {
        return $this->contents ??= file_get_contents($this->path());
    }

    public function write(string $contents): void
    {
        $this->contents = $contents;
        $this->directory->save($this);
    }

    private function path(): string
    {
        return $this->directory->path() . $this->filename;
    }
}
