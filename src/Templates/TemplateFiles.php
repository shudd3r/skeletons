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


class TemplateFiles
{
    private Files $skeleton;
    private array $typeIndex;
    private array $index;

    /**
     * @param Files                        $skeleton
     * @param array<string, array<string>> $typeIndex
     */
    public function __construct(Files $skeleton, array $typeIndex)
    {
        $this->skeleton  = $skeleton;
        $this->typeIndex = $typeIndex;
        $this->index     = $this->fileIndex();
    }

    public function files(array $types): Files
    {
        $index = $types === array_keys($this->typeIndex) ? $this->index : $this->fileIndex($types);
        return new Files\IndexedFiles($this->skeleton, $index);
    }

    public function file(string $filename): File
    {
        return $this->skeleton->file($this->index[$filename] ?? $filename);
    }

    private function fileIndex(array $types = null): array
    {
        $index = [];
        foreach ($this->typeIndex as $type => $typeIndex) {
            if ($types && !in_array($type, $types)) { continue; }
            $index += $typeIndex;
        }
        return $index;
    }
}
