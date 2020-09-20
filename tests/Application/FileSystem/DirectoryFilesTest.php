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
use Shudd3r\PackageFiles\Application\FileSystem\Exception\InvalidAncestorDirectory;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Tests\Doubles;


class DirectoryFilesTest extends TestCase
{
    public function testInstantiation()
    {
        $this->assertInstanceOf(DirectoryFiles::class, new DirectoryFiles(new Doubles\FakeDirectory()));

        $directory = $this->directoryStructure();
        $files     = $directory->files();
        $this->assertInstanceOf(DirectoryFiles::class, new DirectoryFiles($directory, $files));
    }

    public function testInstantiationWithNotMatchingFiles_ThrowsException()
    {
        $directory = new Doubles\FakeDirectory(true, '/invalid/root/path');
        $files     = $this->directoryStructure()->files();
        $this->expectException(InvalidAncestorDirectory::class);
        new DirectoryFiles($directory, $files);
    }

    public function testToArrayMethod_ReturnsRecursivelyFoundFiles()
    {
        $directory  = $this->directoryStructure();
        $collection = new DirectoryFiles($directory);
        $files      = $collection->toArray();

        $this->assertCount(6, $files);

        $expectedFiles = array_merge(
            $directory->files,
            $directory->subdirectories[0]->files,
            $directory->subdirectories[1]->files
        );
        $this->assertSame($expectedFiles, $files);
    }

    public function testFilterMethod()
    {
        $directory  = $this->directoryStructure();
        $collection = new DirectoryFiles($directory);

        $this->assertEquals($collection, $collection->filter(fn(File $file) => true));

        $existingOnly = fn(File $file) => $file->exists();
        $expected     = [$directory->files[1], $directory->subdirectories[1]->files[1]];
        $this->assertSame($expected, $collection->filter($existingOnly)->toArray());
        $this->assertTrue($expected[0]->exists() && $expected[1]->exists());
    }

    public function testWithinDirectoryMethod()
    {
        $directory   = $this->directoryStructure();
        $collection  = new DirectoryFiles($directory);
        $newRootPath = '/new/directory';

        $newCollection = $collection->withinDirectory(new Doubles\FakeDirectory(true, $newRootPath));
        $expectedFiles = [
            new Doubles\MockedFile('', false, '/new/directory/foo1.txt'),
            new Doubles\MockedFile('', false, '/new/directory/foo2.txt'),
            new Doubles\MockedFile('', false, '/new/directory/subDirBar/bar1.txt'),
            new Doubles\MockedFile('', false, '/new/directory/subDirBaz/baz1.txt'),
            new Doubles\MockedFile('', false, '/new/directory/subDirBaz/baz2.txt'),
            new Doubles\MockedFile('', false, '/new/directory/subDirBaz/baz3.txt'),
        ];
        $this->assertEquals($expectedFiles, $newCollection->toArray());
    }

    private function directoryStructure(string $rootPath = '/root/path'): Doubles\FakeDirectory
    {
        $directory = $this->directory($rootPath, 'foo', 2);
        $directory->subdirectories = [
            $this->directory($rootPath . '/subDirBar', 'bar', 1),
            $this->directory($rootPath . '/subDirBaz', 'baz', 3)
        ];

        return $directory;
    }

    private function directory(string $path, string $fileId, int $files): Doubles\FakeDirectory
    {
        $directory  = new Doubles\FakeDirectory(true, $path);

        $toFile = fn(int $num) => $this->file($path, $num, $fileId);
        $directory->files = array_map($toFile, range(1, $files));

        return $directory;
    }

    private function file(string $path, int $num, string $fileId): Doubles\MockedFile
    {
        $name   = $fileId . $num;
        $exists = $num % 2 === 0;
        $path   = $path . '/' . $name . '.txt';
        return new Doubles\MockedFile($name, $exists, $path);
    }
}
