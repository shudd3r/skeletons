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

use Iterator;


class DirectoryFiles implements Iterator
{
    private Directory $directory;

    /** @var File[]  */
    private array $files;
    private int   $idx = 0;

    public function __construct(Directory $directory)
    {
        $this->directory = $directory;
    }

    public function current(): File
    {
        return $this->files[$this->idx];
    }

    public function next(): void
    {
        $this->idx++;
    }

    public function key(): string
    {
        return $this->files[$this->idx]->pathRelativeTo($this->directory);
    }

    public function valid(): bool
    {
        if (!isset($this->files)) { $this->files = $this->readDirectory($this->directory); }
        return isset($this->files[$this->idx]);
    }

    public function rewind(): void
    {
        $this->idx = 0;
    }

    private function readDirectory(Directory $directory): array
    {
        $files = $directory->files();
        foreach ($directory->subdirectories() as $subdirectory) {
            $files = array_merge($files, $this->readDirectory($subdirectory));
        }

        return $files;
    }
}
