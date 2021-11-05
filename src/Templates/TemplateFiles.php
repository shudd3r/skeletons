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
    private Files $skeleton;
    private array $files;
    private array $typeIndex;

    private array $filter    = [];
    private bool  $inclusive = false;

    public function __construct(Files $skeleton, array $files, array $typeIndex)
    {
        $this->skeleton  = $skeleton;
        $this->files     = $files;
        $this->typeIndex = $typeIndex;
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
        return $this->files[$filename] ?? $this->skeleton->file($filename);
    }

    public function fileList(): array
    {
        return array_values($this->filteredFiles());
    }

    private function filteredFiles(): array
    {
        if (!$this->filter) { return $this->files; }

        $filenames = [];
        foreach ($this->filter as $type) {
            $filenames = array_merge($filenames, $this->typeIndex[$type] ?? []);
        }

        $index = array_flip($filenames);
        return $this->inclusive ? array_intersect_key($this->files, $index) : array_diff_key($this->files, $index);
    }
}
