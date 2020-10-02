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


class DirectoryFiles
{
    private array $files;

    /**
     * @param File[]    $files
     */
    public function __construct(array $files)
    {
        $this->files = $files;
    }

    /**
     * @return File[]
     */
    public function toArray(): array
    {
        return $this->files;
    }

    /**
     * @param callable $keepFile fn(File) => bool
     *
     * @return self
     */
    public function filter(callable $keepFile): self
    {
        $files = array_values(array_filter($this->files, $keepFile));
        return new self($files);
    }

    public function withinDirectory(Directory $directory): self
    {
        $contextSwitch = fn(File $file) => $file->reflectedIn($directory);
        return new self(array_map($contextSwitch, $this->files));
    }
}
