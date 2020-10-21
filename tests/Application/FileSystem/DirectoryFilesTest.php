<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\FileSystem;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryFiles;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Tests\Doubles;


class DirectoryFilesTest extends TestCase
{
    public function testToArrayMethod_ReturnsArrayOfFiles()
    {
        $directoryFiles = new DirectoryFiles($files = [new Doubles\MockedFile(), new Doubles\MockedFile()]);
        $this->assertSame($files, $directoryFiles->toArray());
    }

    public function testFilterMethod()
    {
        $directoryFiles = new DirectoryFiles([
            $file1 = Doubles\MockedFile::withContents(),
            Doubles\MockedFile::withContents(null),
            $file3 = Doubles\MockedFile::withContents()
        ]);

        $this->assertEquals($directoryFiles, $directoryFiles->filteredWith(fn(File $file) => true));

        $existingOnly = fn(File $file) => $file->exists();
        $this->assertSame([$file1, $file3], $directoryFiles->filteredWith($existingOnly)->toArray());
    }

    public function testWithinDirectoryMethod()
    {
        $directoryFiles   = new DirectoryFiles([new Doubles\MockedFile(), new Doubles\MockedFile()]);
        $newRootDirectory = new Doubles\FakeDirectory(true, '/new/directory');

        $newCollection = $directoryFiles->reflectedIn($newRootDirectory);
        $expectedFiles = [
            new Doubles\MockedFile('file.txt', $newRootDirectory),
            new Doubles\MockedFile('file.txt', $newRootDirectory),
        ];
        $this->assertEquals($expectedFiles, $newCollection->toArray());
    }
}
