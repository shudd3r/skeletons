<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Environment\Files\Paths;
use Exception;


class FakeDirectory implements Directory
{
    use Paths;

    private string $path;
    private bool   $exists;

    /** @var MockedFile[] */
    private array $files = [];

    /** @var FakeDirectory[] */
    private array $subdirectories = [];

    public function __construct(string $path = '/fake/directory', bool $exists = true)
    {
        $this->exists = $exists;
        $this->path   = $this->normalized($path, DIRECTORY_SEPARATOR, true);
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
        return $this->files[$filename] ?? new MockedFile(null, $filename, $this);
    }

    public function fileList(): array
    {
        foreach ($this->subdirectories as $dirname => $directory) {
            foreach ($directory->fileList() as $file) {
                $this->addFile($dirname . '/' . $file->name(), $file->contents());
            }
        }

        $files = [];
        foreach ($this->files as $filename => $file) {
            $files[] = $this->file($filename);
        }

        return $files;
    }

    public function addFile(string $name, ?string $contents = ''): void
    {
        $name = $this->normalized($name);

        if (isset($this->files[$name])) {
            throw new Exception('File already added');
        }

        $this->files[$name] = new MockedFile($contents, $name, $this);
    }

    public function updateIndex(File $file): void
    {
        $filename = $file->name();
        if (isset($this->files[$filename])) { return; }
        $this->files[$filename] = $file;
    }
}
