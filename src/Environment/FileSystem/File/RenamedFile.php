<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\FileSystem\File;

use Shudd3r\Skeletons\Environment\FileSystem\File;
use Shudd3r\Skeletons\Environment\FileSystem\Paths;


class RenamedFile implements File
{
    use Paths;

    private File   $file;
    private string $name;

    public function __construct(File $file, string $name)
    {
        $this->file = $file;
        $this->name = $this->normalized($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function exists(): bool
    {
        return $this->file->exists();
    }

    public function contents(): string
    {
        return $this->file->contents();
    }

    public function write(string $contents): void
    {
        $this->file->write($contents);
    }
}
