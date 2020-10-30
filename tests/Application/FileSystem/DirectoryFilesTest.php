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
    public function testExistMethod()
    {
        $files = new DirectoryFiles([]);
        $this->assertFalse($files->exist());

        $files = new DirectoryFiles([new Doubles\MockedFile(null), new Doubles\MockedFile(null)]);
        $this->assertFalse($files->exist());

        $files = new DirectoryFiles([new Doubles\MockedFile(null), new Doubles\MockedFile()]);
        $this->assertTrue($files->exist());
    }

    public function testForEachMethod()
    {
        $directoryFiles = new DirectoryFiles($files = [new Doubles\MockedFile(), new Doubles\MockedFile()]);

        $iterated = [];
        $directoryFiles->forEach(function (File $file) use (&$iterated) { $iterated[] = $file; });
        $this->assertSame($files, $iterated);
    }

    public function testFilterMethod()
    {
        $directoryFiles = new DirectoryFiles([
            $file1 = new Doubles\MockedFile(),
            new Doubles\MockedFile(null),
            $file3 = new Doubles\MockedFile()
        ]);

        $this->assertEquals($directoryFiles, $directoryFiles->filteredWith(fn(File $file) => true));

        $existingOnly = fn(File $file) => $file->exists();
        $this->assertEquals(new DirectoryFiles([$file1, $file3]), $directoryFiles->filteredWith($existingOnly));
    }

    public function testReflectedInMethod()
    {
        $rootDirectory  = new Doubles\FakeDirectory('/root/directory');
        $directoryFiles = new DirectoryFiles([
            $file1 = $rootDirectory->file('foo/test.one'),
            $file2 = $rootDirectory->file('bar.two')
        ]);

        $newRootDirectory = new Doubles\FakeDirectory('/new/directory');
        $expectedFiles    = new DirectoryFiles([
            $newRootDirectory->file($file1->name()),
            $newRootDirectory->file($file2->name())
        ]);
        $this->assertEquals($expectedFiles, $directoryFiles->reflectedIn($newRootDirectory));
    }
}
