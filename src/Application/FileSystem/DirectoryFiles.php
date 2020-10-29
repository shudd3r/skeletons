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
     * @param File[] $files
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

    public function exist(): bool
    {
        foreach ($this->files as $file) {
            if ($file->exists()) { return true; }
        }

        return false;
    }

    /**
     * @param callable $callback fn(File) => void
     */
    public function forEach(callable $callback): void
    {
        array_walk($this->files, $callback);
    }

    /**
     * @param callable $isValidFile fn(File) => bool
     *
     * @return self
     */
    public function filteredWith(callable $isValidFile): self
    {
        $files = array_values(array_filter($this->files, $isValidFile));
        return new self($files);
    }

    public function reflectedIn(Directory $directory): self
    {
        $contextSwitch = fn(File $file) => $directory->file($file->name());
        return new self(array_map($contextSwitch, $this->files));
    }
}
