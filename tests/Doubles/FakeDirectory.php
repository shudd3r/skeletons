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
use Closure;
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
        $name = $this->normalized($name);
        if (isset($this->subdirectories[$name])) { return $this->subdirectories[$name]; }
        $directory = new self($this->path . '/' . $name, false);
        foreach ($this->files as $filename => $file) {
            if (strpos($filename, $name . '/') !== 0) { continue; }
            $relativeName = substr($filename, strlen($name) + 1);
            if ($directory->file($relativeName)->exists()) { continue; }
            $directory->addFile($relativeName, $file->contents());
        }
        return $this->subdirectories[$name] = $directory;
    }

    public function file(string $filename): File
    {
        return $this->files[$filename] ?? new MockedFile(null, $filename, $this);
    }

    public function fileList(): array
    {
        foreach ($this->subdirectories as $dirname => $directory) {
            foreach ($directory->fileList() as $file) {
                $filename = $dirname . '/' . $file->name();
                if (isset($this->files[$filename])) { continue; }
                $this->addFile($filename, $file->contents());
            }
        }

        $files = [];
        foreach ($this->files as $filename => $file) {
            if (!$file->exists()) { continue; }
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

    public function removeFile(string $name): void
    {
        unset($this->files[$name]);
        $this->updateSubdirectories($name, fn ($targetFile) => $targetFile->remove());
    }

    public function updateIndex(File $file): void
    {
        $filename = $file->name();
        $this->files[$filename] = $file;
        $this->updateSubdirectories($filename, fn ($targetFile) => $targetFile->write($file->contents()));
    }

    private function updateSubdirectories(string $filename, Closure $procedure): void
    {
        if (!strpos($filename, '/')) { return; }
        foreach ($this->subdirectories as $name => $directory) {
            if (strpos($filename, $name . '/') !== 0) { continue; }
            $relativeName = substr($filename, strlen($name) + 1);
            $procedure($directory->file($relativeName));
        }
    }
}
