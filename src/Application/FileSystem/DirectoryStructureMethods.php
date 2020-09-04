<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem;


trait DirectoryStructureMethods
{
    private function createDirectoryStructure(string $path): void
    {
        $missingDir = [];
        while (!is_dir($path)) {
            $missingDir[] = basename($path);
            $path = dirname($path);
        }

        foreach (array_reverse($missingDir) as $directory) {
            mkdir($path .= DIRECTORY_SEPARATOR . $directory);
        }
    }
}
