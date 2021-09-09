<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Exception;


class FakeDirectory implements Directory
{
    public string $path;
    public bool   $exists;

    /** @var MockedFile[] */
    public array $files = [];

    /** @var FakeDirectory[] */
    public array $subdirectories = [];

    public function __construct(string $path = '/fake/directory', bool $exists = true)
    {
        $this->exists = $exists;
        $this->path   = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function subdirectory(string $name): self
    {
        return $this->subdirectories[$name] ??= new self($this->path . '/' . $name, false);
    }

    public function file(string $filename): File
    {
        $file = $this->files[$filename] ?? new MockedFile(null);

        $file->name = $filename;
        $file->root = $this;

        return $file;
    }

    public function files(): array
    {
        foreach ($this->subdirectories as $dirname => $directory) {
            foreach ($directory->files() as $file) {
                $this->addFile($dirname . '/' . $file->name(), $file->contents());
            }
        }

        $files = [];
        foreach ($this->files as $filename => $file) {
            $files[] = $this->file($filename);
        }

        return $files;
    }

    public function addFile(string $name, string $contents = ''): void
    {
        $file = $this->file($name);
        if ($file->exists()) {
            throw new Exception('File already exists');
        }

        $file->write($contents);
    }
}
