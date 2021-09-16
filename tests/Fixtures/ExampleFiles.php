<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Fixtures;

use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Tests\Doubles\FakeDirectory;


class ExampleFiles
{
    private Directory $directory;

    public function __construct(string $directory)
    {
        $this->directory = new Directory\LocalDirectory(__DIR__ . DIRECTORY_SEPARATOR . $directory);
    }

    public function directory(string $name): FakeDirectory
    {
        $fakeDirectory = new FakeDirectory('/' . $name);
        foreach ($this->directory->subdirectory($name)->files() as $file) {
            $fakeDirectory->addFile(str_replace('\\', '/', $file->name()), $file->contents());
        }

        return $fakeDirectory;
    }

    public function contentsOf(string $filename): string
    {
        return $this->directory->file($filename)->contents();
    }
}
