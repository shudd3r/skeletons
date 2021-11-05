<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates;

use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Environment\Files\File;


class TemplateFiles implements Files
{
    private const EXT = '.sk_';

    private Files $skeleton;
    private array $files;
    private array $directives;

    private array $typeIndex = [
        'file'  => [],
        'local' => [],
        'init'  => []
    ];

    private array $filter    = [];
    private bool  $inclusive = false;

    public function __construct(Files $skeleton)
    {
        $this->skeleton   = $skeleton;
        $this->directives = $this->directives();
    }

    public function withFilter(array $types, bool $inclusive): self
    {
        $files = clone $this;

        $files->filter    = $types;
        $files->inclusive = $inclusive;

        return $files;
    }

    public function file(string $filename): File
    {
        $files = $this->files ??= $this->templateFiles();
        return $files[$filename] ?? $this->skeleton->file($filename);
    }

    public function fileList(): array
    {
        return array_values($this->filteredFiles());
    }

    private function filteredFiles(): array
    {
        $this->files ??= $this->templateFiles();
        if (!$this->filter) { return $this->files; }

        $filenames = [];
        foreach ($this->filter as $type) {
            $filenames = array_merge($filenames, $this->typeIndex[$type] ?? []);
        }

        $index = array_flip($filenames);
        return $this->inclusive ? array_intersect_key($this->files, $index) : array_diff_key($this->files, $index);
    }

    private function templateFiles(): array
    {
        $files = [];
        foreach ($this->skeleton->fileList() as $originalFile) {
            $originalName = $originalFile->name();
            $filename     = $this->targetPath($originalName);
            $direct       = $originalName === $filename;
            $files[$filename] = $direct ? $originalFile : new Files\File\RenamedFile($originalFile, $filename);
        }

        return $files;
    }

    private function targetPath(string $filename): string
    {
        $extFound = strrpos($filename, self::EXT);
        if (!$extFound) { return $filename; }

        $directive = substr($filename, $extFound);
        if (!in_array($directive, $this->directives)) { return $filename; }
        $filename = $this->unlockedPath(substr($filename, 0, -strlen($directive)));

        $type = substr($directive, strlen(self::EXT));
        $this->typeIndex[$type][] = $filename;

        return $filename;
    }

    private function directives(): array
    {
        return explode(',', self::EXT . implode(',' . self::EXT, array_keys($this->typeIndex)));
    }

    private function unlockedPath(string $filename): string
    {
        $protectedSuffix = self::EXT . 'dir';
        return str_replace($protectedSuffix, '', $filename);
    }
}
