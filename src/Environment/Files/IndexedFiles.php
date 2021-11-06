<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\Files;

use Shudd3r\Skeletons\Environment\Files;


class IndexedFiles implements Files
{
    private Files $source;
    private array $index;

    /**
     * @param Files                 $source
     * @param array<string, string> $index
     */
    public function __construct(Files $source, array $index)
    {
        $this->source = $source;
        $this->index  = $index;
    }

    public function file(string $filename): File
    {
        $renamed = isset($this->index[$filename]) && $this->index[$filename] !== $filename;
        return $renamed ? $this->renamed($filename, $this->index[$filename]) : $this->source->file($filename);
    }

    public function fileList(): array
    {
        $files = [];
        foreach ($this->index as $filename => $original) {
            $files[] = $filename === $original ? $this->source->file($filename) : $this->renamed($filename, $original);
        }

        return $files;
    }

    private function renamed($filename, $original): File
    {
        return new Files\File\RenamedFile($this->source->file($original), $filename);
    }
}
