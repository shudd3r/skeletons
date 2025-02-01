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

    public function test_path_returns_normalized_directory_path(): void
    {
        $directory = new VirtualDirectory('/some/path\foo\bar');
        $this->assertSame($this->normalized('/some/path/foo/bar', DIRECTORY_SEPARATOR, true), $directory->path());
    }

    public function test_exists_returns_true_for_existing_directories(): void
    {
        $directory = new VirtualDirectory('/some/path', false);
        $this->assertFalse($directory->exists());
        $directory = new VirtualDirectory('/some/path', true);
        $this->assertTrue($directory->exists());
    }

    public function test_files_can_be_added_and_removed_from_directory(): void
    {
        $directory = new VirtualDirectory();

        $expectedFile = new VirtualFile('foo.file', null, $directory);
        $this->assertEmpty($directory->fileList());
        $this->assertEquals($expectedFile, $directory->file('foo.file'));
        $this->assertFalse($directory->file('foo.file')->exists());

        $directory->addFile('foo.file', 'contents');

        $expectedFiles = [new VirtualFile('foo.file', 'contents', $directory)];
        $this->assertEquals($expectedFiles, $directory->fileList());
        $this->assertEquals($expectedFiles[0], $directory->file('foo.file'));
        $this->assertTrue($directory->file('foo.file')->exists());

        $directory->addFile('bar.file');

        $expectedFiles = [$expectedFiles[0], new VirtualFile('bar.file', '', $directory)];
        $this->assertEquals($expectedFiles, $directory->fileList());
        $this->assertEquals($expectedFiles[1], $directory->file('bar.file'));
        $this->assertTrue($directory->file('bar.file')->exists());

        $directory->removeFile('foo.file');
        $this->assertEquals([$expectedFiles[1]], $directory->fileList());
        $this->assertFalse($directory->file('foo.file')->exists());
    }

    public function test_adding_file_twice_throws_Exception(): void
    {
        $directory = new VirtualDirectory();
        $directory->addFile('foo.txt');
        $this->expectException(LogicException::class);
        $directory->addFile('foo.txt');
    }

    public function test_adding_file_creates_directory_that_is_not_removed_when_file_is_deleted(): void
    {
        $directory = new VirtualDirectory('/some/path', false);
        $directory->addFile('foo.txt');
        $this->assertTrue($directory->exists());
        $directory->removeFile('foo.txt');
        $this->assertTrue($directory->exists());
    }

    public function test_subdirectory_returns_directory_with_extended_path_that_does_not_exist_without_files(): void
    {
        $directory    = new VirtualDirectory('/root');
        $subdirectory = $directory->subdirectory('foo/bar');
        $this->assertSame($this->normalized('/root/foo/bar', DIRECTORY_SEPARATOR, true), $subdirectory->path());
        $this->assertFalse($subdirectory->exists());
    }

    public function test_adding_and_removing_files_from_subdirectory(): void
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

    public function test_subdirectory_instance_contain_files_created_in_parent_directory(): void
    {
        $directory = new VirtualDirectory();
        $directory->addFile('foo/bar/baz.txt');
        $directory->addFile('foo/bar/sub/file.txt');

        $subdirectory = $directory->subdirectory('foo/bar');

        $expectedFiles = [
            new VirtualFile('baz.txt', '', $subdirectory),
            new VirtualFile('sub/file.txt', '', $subdirectory)
        ];
        $this->assertEquals($expectedFiles, $subdirectory->fileList());
    }

    public function test_writing_file_contents_creates_file_in_all_contexts(): void
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

    public function test_remove_method_on_file_instance_removes_file_from_all_contexts(): void
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

    public function test_files_without_subdirectories_are_not_filtered_by_synchronization(): void
    {
        $directory    = new VirtualDirectory();
        $subdirectory = $directory->subdirectory('foo');

        $directory->addFile('foo/bar.txt');
        $directory->addFile('root.file');
        $directory->addFile('fizz/buzz.file');

        $this->assertCount(1, $subdirectory->fileList());
        $this->assertCount(3, $directory->fileList());
    }

    public function test_instantiation_with_file_list_adds_files_filtering_test_postfix(): void
    {
        $files = [
            new VirtualFile('foo.txt.sk_tests', 'foo contents'),
            new VirtualFile('foo.sk_tests/bar.txt', 'bar contents'),
            new VirtualFile('baz.sk_tests', 'baz contents')
        ];

        $directory = VirtualDirectory::withFiles($files);

        $expected = new VirtualDirectory();
        $expected->addFile('foo.txt', 'foo contents');
        $expected->addFile('foo/bar.txt', 'bar contents');
        $expected->addFile('baz', 'baz contents');

        $this->assertEquals($expected, $directory);
    }
}
