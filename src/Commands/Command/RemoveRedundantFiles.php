<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Command;

use Shudd3r\Skeletons\Commands\Command;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Environment\Files\Directory;


class RemoveRedundantFiles implements Command
{
    use Files\Paths;

    private Directory $package;
    private Files     $files;

    public function __construct(Directory $package, Files $files)
    {
        $this->package = $package;
        $this->files   = $files;
    }

    public function execute(): void
    {
        foreach ($this->fileIndex() as $subdirectory => $files) {
            if ($this->essentialDummies($subdirectory, $files)) { continue; }
            array_walk($files, fn (Files\File $file) => $file->remove());
        }
    }

    private function fileIndex(): array
    {
        $fileIndex = [];
        foreach ($this->files->fileList() as $file) {
            $filename = $file->name();
            if (!$this->package->file($filename)->exists()) { continue; }
            if (!strpos($filename, '/')) { continue; }

            $systemPath = $this->normalized($filename, DIRECTORY_SEPARATOR);
            $dirname    = dirname($systemPath);
            $filename   = basename($systemPath);

            $fileIndex[$dirname][$filename] = $file;
        }

        return $fileIndex;
    }

    private function essentialDummies(string $subdirectory, array $dummyFiles): bool
    {
        $packageFiles = $this->package->subdirectory($subdirectory)->fileList();
        foreach ($packageFiles as $idx => $file) {
            $filename = $file->name();
            if (!isset($dummyFiles[$filename])) { continue; }
            unset($packageFiles[$idx]);
        }

        return empty($packageFiles);
    }
}
