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
use Shudd3r\Skeletons\Environment\Files\Directory;


class DummyFiles
{
    private Files $files;

    public function __construct(Files $files)
    {
        $this->files = $files;
    }

    public function verifiedFiles(Directory $package): VerifiedDummyFiles
    {
        $redundantFiles = [];
        $missingFiles   = [];
        foreach ($this->files->fileList() as $file) {
            $filename  = $file->name();
            $directory = dirname($filename);
            if ($directory === '.') { continue; }
            $count = count($package->subdirectory($directory)->fileList());

            if ($count === 0) {
                $missingFiles[] = $file;
                continue;
            }

            if ($count === 1 || !$package->file($filename)->exists()) { continue; }
            $redundantFiles[] = $file;
        }

        return new VerifiedDummyFiles($redundantFiles, $missingFiles);
    }
}
