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
    private Directory $directory;
    private array $files;

    /**
     * @param Directory $directory
     * @param File[]    $files
     */
    public function __construct(Directory $directory, array $files = null)
    {
        $this->directory = $directory;
        $this->files     = $files ? $this->validScopeFiles($files) : $this->readDirectory($this->directory);
    }

    /**
     * @return File[]
     */
    public function toArray(): array
    {
        return $this->files;
    }

    public function filter(callable $keepFile): self
    {
        return new self($this->directory, array_values(array_filter($this->files, $keepFile)));
    }

    public function withinDirectory(Directory $directory): self
    {
        $contextSwitch = fn(File $file) => $directory->file($file->pathRelativeTo($this->directory));
        return new self($directory, array_map($contextSwitch, $this->files));
    }

    private function readDirectory(Directory $directory): array
    {
        $files = $directory->files();
        foreach ($directory->subdirectories() as $subdirectory) {
            $files = array_merge($files, $this->readDirectory($subdirectory));
        }

        return $files;
    }

    private function validScopeFiles(array $files): array
    {
        $directoryPath = $this->directory->path();
        foreach ($files as $file) {
            if (strpos($file->path(), $directoryPath) !== 0) {
                throw new Exception\InvalidAncestorDirectory();
            }
        }

        return $files;
    }
}
