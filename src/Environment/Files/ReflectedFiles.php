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


class ReflectedFiles implements Files
{
    private Files $target;
    private Files $origin;

    public function __construct(Files $target, Files $origin)
    {
        $this->target = $target;
        $this->origin = $origin;
    }

    public function file(string $filename): File
    {
        return $this->target->file($filename);
    }

    public function fileList(): array
    {
        $targetFiles = [];
        foreach ($this->origin->fileList() as $originFile) {
            if (!$originFile->exists()) { continue; }
            $targetFiles[] = $this->target->file($originFile->name());
        }

        return $targetFiles;
    }
}
