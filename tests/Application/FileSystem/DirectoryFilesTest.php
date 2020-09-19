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

    private function directoryStructure(): Doubles\FakeDirectory
    {
        $directory = $this->directory('/root/path', 'foo', 2);
        $directory->subdirectories = [
            $this->directory('/root/path/subDirBar', 'bar', 1),
            $this->directory('/root/path/subDirBaz', 'baz', 3)
        ];

        return $directory;
    }

    private function directory(string $path, string $fileId, int $files): Doubles\FakeDirectory
    {
        $directory  = new Doubles\FakeDirectory(true, $path);

        $toFile = fn(int $num) => new Doubles\MockedFile($fileId . $num, true, $path . '/' . $fileId . $num . '.txt');
        $directory->files = array_map($toFile, range(1, $files));

        return $directory;
    }
}
