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
        foreach ($this->relevantDummyFiles() as $directory => $file) {
            $count       = count($package->subdirectory($directory)->fileList());
            $isMissing   = $count === 0;
            $isRedundant = $count > 1 && $package->file($file->name())->exists();

            if (!$isMissing && !$isRedundant) { continue; }
            $isMissing ? array_push($missingFiles, $file) : array_push($redundantFiles, $file);
        }

        return new VerifiedDummyFiles($redundantFiles, $missingFiles);
    }

    /** @return array<string, Files\File> */
    private function relevantDummyFiles(): array
    {
        $files = [];
        foreach ($this->files->fileList() as $file) {
            $filename  = $file->name();
            $directory = dirname($filename);
            if ($directory === '.') { continue; }

            foreach ($files as $listedDirectory => $listedFile) {
                if (strpos($directory, $listedDirectory) === 0) { unset($files[$listedDirectory]); }
                if (strpos($listedDirectory, $directory) === 0) { continue 2; }
            }

            $files[$directory] = $file;
        }
        return $files;
    }
}
