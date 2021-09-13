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

    public function hasSameFilesAs(Directory $directory): bool
    {
        $givenFiles = $directory->files();

        if (count($givenFiles) !== count($this->directory->files())) {
            echo 'Directories are not the same: File number mismatch';
            return false;
        }

        foreach ($givenFiles as $file) {
            $givenContents     = $file->contents();
            $directoryContents = $this->directory->file($file->name())->contents();

            if ($givenContents !== $directoryContents) {
                echo 'expected: ---------------' . PHP_EOL . $directoryContents . PHP_EOL;
                echo 'given: ------------------' . PHP_EOL . $givenContents . PHP_EOL;
                echo '-------------------------';
                return false;
            }
        }
        return true;
    }

    public function contentsOf(string $filename): string
    {
        return $this->directory->file($filename)->contents();
    }
}
