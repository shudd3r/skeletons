<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Environment\Files;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Environment\Files\Directory\VirtualDirectory;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;
use Shudd3r\Skeletons\Environment\Files\Paths;
use LogicException;


class VirtualFilesTest extends TestCase
{
    use Paths;

    public function testPathMethod_ReturnsNormalizedDirectoryPath()
    {
        $directory = new VirtualDirectory('/some/path\foo\bar');
        $this->assertSame($this->normalized('/some/path/foo/bar', DIRECTORY_SEPARATOR, true), $directory->path());
    }

    public function testExistsMethod_ReturnsTrueIfDirectoryExists()
    {
        $directory = new VirtualDirectory('/some/path', false);
        $this->assertFalse($directory->exists());
        $directory = new VirtualDirectory('/some/path', true);
        $this->assertTrue($directory->exists());
    }

    public function testFilesCanBeAddedAndRemovedFromDirectory()
    {
        $directory = new VirtualDirectory();

        $expectedFile = new VirtualFile(null, 'foo.file', $directory);
        $this->assertEmpty($directory->fileList());
        $this->assertEquals($expectedFile, $directory->file('foo.file'));
        $this->assertFalse($directory->file('foo.file')->exists());

        $directory->addFile('foo.file', 'contents');

        $expectedFiles = [new VirtualFile('contents', 'foo.file', $directory)];
        $this->assertEquals($expectedFiles, $directory->fileList());
        $this->assertEquals($expectedFiles[0], $directory->file('foo.file'));
        $this->assertTrue($directory->file('foo.file')->exists());

        $directory->addFile('bar.file');

        $expectedFiles = [$expectedFiles[0], new VirtualFile('', 'bar.file', $directory)];
        $this->assertEquals($expectedFiles, $directory->fileList());
        $this->assertEquals($expectedFiles[1], $directory->file('bar.file'));
        $this->assertTrue($directory->file('bar.file')->exists());

        $directory->removeFile('foo.file');
        $this->assertEquals([$expectedFiles[1]], $directory->fileList());
        $this->assertFalse($directory->file('foo.file')->exists());
    }

    public function testAddingFileTwice_ThrowsException()
    {
        $directory = new VirtualDirectory();
        $directory->addFile('foo.txt');
        $this->expectException(LogicException::class);
        $directory->addFile('foo.txt');
    }

    public function testAddingFileMakeDirectoryExistAndRemovingFileDoesntChangeIt()
    {
        $directory = new VirtualDirectory('/some/path', false);
        $directory->addFile('foo.txt');
        $this->assertTrue($directory->exists());
        $directory->removeFile('foo.txt');
        $this->assertTrue($directory->exists());
    }

    public function testSubdirectoryMethod_ReturnsSubdirectoryWithExtendedPathThatDoesNotExistWithoutFiles()
    {
        $directory    = new VirtualDirectory('/root');
        $subdirectory = $directory->subdirectory('foo/bar');
        $this->assertSame($this->normalized('/root/foo/bar', DIRECTORY_SEPARATOR, true), $subdirectory->path());
        $this->assertFalse($subdirectory->exists());
    }

    public function testAddingAndRemovingFilesFromSubdirectory()
    {
        $directory    = new VirtualDirectory();
        $subdirectory = $directory->subdirectory('foo/bar');

        $subdirectory->addFile('baz.txt');

        $this->assertTrue($subdirectory->exists());
        $this->assertTrue($directory->exists());
        $this->assertTrue($directory->file('foo/bar/baz.txt')->exists());

        $subdirectory->removeFile('baz.txt');
        $this->assertFalse($directory->file('foo/bar/baz.txt')->exists());
    }

    public function testSubdirectoryInstance_ContainsFilesCreatedInParentDirectory()
    {
        $directory = new VirtualDirectory();
        $directory->addFile('foo/bar/baz.txt');
        $directory->addFile('foo/bar/sub/file.txt');

        $subdirectory = $directory->subdirectory('foo/bar');

        $expectedFiles = [
            new VirtualFile('', 'baz.txt', $subdirectory),
            new VirtualFile('', 'sub/file.txt', $subdirectory)
        ];
        $this->assertEquals($expectedFiles, $subdirectory->fileList());
    }

    public function testWritingFileContents_CreatesFileInAllContexts()
    {
        $directory    = new VirtualDirectory();
        $subdirectory = $directory->subdirectory('foo/bar');

        $this->assertFalse($directory->file('foo/bar/baz.txt')->exists());
        $this->assertFalse($subdirectory->file('baz.txt')->exists());

        $directory->file('foo/bar/baz.txt')->write('both contexts content');
        $subdirectory->file('other.txt')->write('other saved in both');

        $this->assertSame('both contexts content', $directory->file('foo/bar/baz.txt')->contents());
        $this->assertSame('both contexts content', $subdirectory->file('baz.txt')->contents());
        $this->assertSame('other saved in both', $directory->file('foo/bar/other.txt')->contents());
        $this->assertSame('other saved in both', $subdirectory->file('other.txt')->contents());
    }

    public function testRemoveMethodOnFile_RemovesFileFromAllContexts()
    {
        $directory    = new VirtualDirectory();
        $subdirectory = $directory->subdirectory('foo/bar');

        $directory->addFile('foo/bar/baz.txt');
        $subdirectory->addFile('sub/file.txt');

        $subdirectory->file('baz.txt')->remove();
        $directory->file('foo/bar/sub/file.txt')->remove();

        $this->assertEmpty($directory->fileList());
        $this->assertEmpty($subdirectory->fileList());
    }

    public function testFilesWithoutSubdirectories_AreNotFilteredBySynchronization()
    {
        $directory    = new VirtualDirectory();
        $subdirectory = $directory->subdirectory('foo');

        $directory->addFile('foo/bar.txt');
        $directory->addFile('root.file');
        $directory->addFile('fizz/buzz.file');

        $this->assertCount(1, $subdirectory->fileList());
        $this->assertCount(3, $directory->fileList());
    }

    public function testInstantiationWithFileList_AddsFilesFilteringTestPostfix()
    {
        $files = [
            new VirtualFile('foo contents', 'foo.txt.sk_tests'),
            new VirtualFile('bar contents', 'foo.sk_tests/bar.txt'),
            new VirtualFile('baz contents', 'baz.sk_tests')
        ];

        $directory = VirtualDirectory::withFiles($files);

        $expected = new VirtualDirectory();
        $expected->addFile('foo.txt', 'foo contents');
        $expected->addFile('foo/bar.txt', 'bar contents');
        $expected->addFile('baz', 'baz contents');

        $this->assertEquals($expected, $directory);
    }
}
