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

use Shudd3r\Skeletons\Environment\FileSystem\Directory;
use Shudd3r\Skeletons\Tests\Doubles\FakeDirectory;


class ExampleFiles
{
    private Directory $directory;

    public function __construct(string $directory)
    {
        $this->directory = new Directory\LocalDirectory(__DIR__ . '/' . $directory);
    }

    public function directory(string $name): FakeDirectory
    {
        $fakeDirectory = new FakeDirectory('/root/directory/' . $name);
        foreach ($this->directory->subdirectory($name)->fileList() as $file) {
            $fakeDirectory->addFile($this->productionName($file->name()), $file->contents());
        }

        return $fakeDirectory;
    }

    public function contentsOf(string $filename): string
    {
        return $this->directory->file($filename)->contents();
    }

    private function productionName(string $name): string
    {
        return str_replace('.sk_tests', '', $name);
    }
}
