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
            $file1 = new Doubles\MockedFile('', true),
            new Doubles\MockedFile('', false),
            $file3 = new Doubles\MockedFile('', true)
        ]);

        $this->assertEquals($directoryFiles, $directoryFiles->filter(fn(File $file) => true));

        $existingOnly = fn(File $file) => $file->exists();
        $this->assertSame([$file1, $file3], $directoryFiles->filter($existingOnly)->toArray());
    }

    public function testWithinDirectoryMethod()
    {
        $directoryFiles   = new DirectoryFiles([new Doubles\MockedFile(), new Doubles\MockedFile()]);
        $newRootDirectory = new Doubles\FakeDirectory(true, '/new/directory');

        $newCollection = $directoryFiles->withinDirectory($newRootDirectory);
        $expectedFiles = [
            new Doubles\MockedFile('', true, '/new/directory/file.txt'),
            new Doubles\MockedFile('', true, '/new/directory/file.txt'),
        ];
        $this->assertEquals($expectedFiles, $newCollection->toArray());
    }
}
