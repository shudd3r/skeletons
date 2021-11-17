<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\Files\Directory;

use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Environment\Files\Paths;
use LogicException;
use Closure;


class VirtualDirectory implements Directory
{
    use Paths;

    protected const TEST_EXT = '.sk_tests';

    private string $path;
    private bool   $exists;

    /** @var VirtualFile[] */
    private array $files = [];

    /** @var VirtualDirectory[] */
    private array $subdirectories = [];

    public function __construct(string $path = '/virtual/directory', bool $exists = true)
    {
        $this->exists = $exists;
        $this->path   = $this->normalized($path, DIRECTORY_SEPARATOR, true);
    }

    /**
     * @param File[] $files
     */
    public static function withFiles(array $files, string $path = '/virtual/directory'): self
    {
        $directory = new self($path);
        foreach ($files as $file) {
            $directory->addFile(str_replace(self::TEST_EXT, '', $file->name()), $file->contents());
        }

        return $directory;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        $this->synchronizeFiles();
        return $this->exists;
    }

    public function subdirectory(string $name): self
    {
        $name      = $this->normalized($name);
        $directory = $this->subdirectories[$name] ??= new self($this->path . '/' . $name, false);
        foreach ($this->files as $filename => $file) {
            if (strpos($filename, $name . '/') !== 0) { continue; }
            $relativeName = substr($filename, strlen($name) + 1);
            if ($directory->file($relativeName)->exists()) { continue; }
            $directory->addFile($relativeName, $file->contents());
        }
        return $directory;
    }

    public function file(string $filename): File
    {
        $this->synchronizeFiles();
        return $this->files[$filename] ?? new VirtualFile($filename, null, $this);
    }

    public function fileList(): array
    {
        $this->synchronizeFiles();
        return array_values($this->files);
    }

    public function addFile(string $filename, string $contents = ''): void
    {
        $filename = $this->normalized($filename);

        if (isset($this->files[$filename])) {
            throw new LogicException('File already added');
        }

        $this->exists = true;
        $this->files[$filename] = new VirtualFile($filename, $contents, $this);
        $this->updateSubdirectories($filename, fn ($targetFile) => $targetFile->write($contents));
    }

    public function removeFile(string $filename): void
    {
        unset($this->files[$filename]);
        $this->updateSubdirectories($filename, fn ($targetFile) => $targetFile->remove());
    }

    public function updateIndex(VirtualFile $file): void
    {
        $filename = $file->name();
        $this->files[$filename] ??= $file;
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

    private function synchronizeFiles(): void
    {
        if (!$this->subdirectories) { return; }

        $filenames = [];
        foreach ($this->subdirectories as $dirname => $directory) {
            foreach ($directory->fileList() as $file) {
                $filename = $dirname . '/' . $file->name();
                $filenames[$filename] = true;
                if (isset($this->files[$filename])) { continue; }
                $this->addFile($filename, $file->contents());
            }
        }

        foreach ($this->files as $filename => $file) {
            if (isset($filenames[$filename])) { continue; }
            foreach (array_keys($this->subdirectories) as $dirname) {
                if (strpos($filename, $dirname . '/') !== 0) { continue; }
                $this->removeFile($filename);
            }
        }
    }
}
