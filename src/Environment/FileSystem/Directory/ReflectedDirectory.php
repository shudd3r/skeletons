<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\FileSystem\Directory;

use Shudd3r\Skeletons\Environment\FileSystem\Directory;
use Shudd3r\Skeletons\Environment\FileSystem\File;


class ReflectedDirectory implements Directory
{
    private Directory $target;
    private Directory $origin;

    public function __construct(Directory $target, Directory $origin)
    {
        $this->target = $target;
        $this->origin = $origin;
    }

    public function path(): string
    {
        return $this->target->path();
    }

    public function exists(): bool
    {
        return $this->target->exists();
    }

    public function subdirectory(string $name): Directory
    {
        return new self($this->target->subdirectory($name), $this->origin->subdirectory($name));
    }

    public function file(string $filename): File
    {
        return $this->target->file($filename);
    }

    public function files(): array
    {
        $targetFiles = [];
        foreach ($this->origin->files() as $originFile) {
            if (!$originFile->exists()) { continue; }
            $targetFiles[] = $this->target->file($originFile->name());
        }

        return $targetFiles;
    }
}
