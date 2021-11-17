<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Fixtures;

use Shudd3r\Skeletons\Environment\Files\Directory;


class ExampleFiles
{
    private Directory $directory;

    public function __construct(string $directory)
    {
        $this->directory = new Directory\LocalDirectory(__DIR__ . '/' . $directory);
    }

    public function directory(string $name = null): Directory\VirtualDirectory
    {
        $dirname = $name ? '/root/directory/' . $name : '/dummy/directory';
        $files   = $name ? $this->directory->subdirectory($name)->fileList() : [];
        return Directory\VirtualDirectory::withFiles($files, $dirname);
    }

    public function contentsOf(string $filename): string
    {
        return $this->directory->file($filename)->contents();
    }
}
