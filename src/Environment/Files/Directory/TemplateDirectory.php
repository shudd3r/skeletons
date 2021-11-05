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
use Shudd3r\Skeletons\Environment\Files\File;


class TemplateDirectory implements Directory
{
    private const EXT = '.sk_';

    private Directory $skeleton;
    private array     $ignored;
    private array     $directives;

    /** @var File[] */
    private array $templateFiles;

    public function __construct(Directory $skeleton, array $ignore = [])
    {
        $this->skeleton   = $skeleton;
        $this->ignored    = $ignore;
        $this->directives = $this->prefixedList(['file', 'local', 'init']);
    }

    public function path(): string
    {
        return $this->skeleton->path();
    }

    public function exists(): bool
    {
        return $this->skeleton->exists();
    }

    public function subdirectory(string $name): Directory
    {
        return new self($this->skeleton->subdirectory($name), $this->ignored);
    }

    public function file(string $filename): File
    {
        $this->templateFiles ??= $this->templateFiles();
        return $this->templateFiles[$filename] ?? $this->skeleton->file($filename);
    }

    public function fileList(): array
    {
        return array_values($this->templateFiles ??= $this->templateFiles());
    }

    private function templateFiles(): array
    {
        $ignored = $this->prefixedList($this->ignored);

        $files = [];
        foreach ($this->skeleton->fileList() as $originalFile) {
            if (!$file = $this->templateFile($originalFile, $ignored)) { continue; }
            $files[$file->name()] = $file;
        }

        return $files;
    }

    private function templateFile(File $file, array $ignored): ?File
    {
        $filename = $file->name();
        $extFound = strrpos($filename, self::EXT);
        if (!$extFound) { return $file; }

        $directive = substr($filename, $extFound);
        if (!in_array($directive, $this->directives)) { return $file; }
        if (in_array($directive, $ignored)) { return null; }

        $packageFilename = $this->unlockedPath(substr($filename, 0, -strlen($directive)));
        return new File\RenamedFile($file, $packageFilename);
    }

    private function prefixedList(array $list): array
    {
        return $list ? explode(',', self::EXT . implode(',' . self::EXT, $list)) : [];
    }

    private function unlockedPath(string $filename): string
    {
        $protectedSuffix = self::EXT . 'dir';
        return str_replace($protectedSuffix, '', $filename);
    }
}
