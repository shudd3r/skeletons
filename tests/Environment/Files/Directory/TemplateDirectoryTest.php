<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Environment\Files\Directory;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Environment\Files\Directory\TemplateDirectory;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Tests\Doubles\FakeDirectory;


class TemplateDirectoryTest extends TestCase
{
    public function testForTemplateExtensionFile_FileMethod_ReturnsFileFromWrappedDirectoryWithoutExtension()
    {
        $wrapped = new FakeDirectory();
        $wrapped->addFile('file.txt.sk_file', 'contents');

        $directory = $this->directory($wrapped);
        $file      = $directory->file('file.txt');

        $this->assertInstanceOf(File\RenamedFile::class, $file);
        $this->assertSame('file.txt', $file->name());
        $this->assertSame('contents', $file->contents());
    }

    public function testProtectedPathsAreUnlocked()
    {
        $wrapped = new FakeDirectory();
        $wrapped->addFile('.git.sk_dir/hooks/pre-commit.sk_local', 'contents');

        $files = $this->directory($wrapped)->fileList();
        $this->assertFilename('.git/hooks/pre-commit', $files[0]);
    }

    public function testFileListMethod_ReturnsFilesWithoutTemplateExtensionsFromWrappedDirectory()
    {
        $directory = $this->directory($this->filesystemSetup());

        $expectedFilenames = ['foo.txt', 'bar.txt', 'other.sk_undefined', 'untracked/baz.txt'];
        foreach($directory->fileList() as $idx => $file) {
            $this->assertFilename($expectedFilenames[$idx], $file);
            $this->assertSame($directory->file($expectedFilenames[$idx]), $file);
        }
    }

    public function testFilesWithIgnoredTemplateExtensions_AreFiltered()
    {
        $directory = $this->directory($this->filesystemSetup(), ['local', 'init']);

        $expectedFilenames = ['foo.txt', 'other.sk_undefined'];
        foreach($directory->fileList() as $idx => $file) {
            $this->assertFilename($expectedFilenames[$idx], $file);
            $this->assertSame($directory->file($expectedFilenames[$idx]), $file);
        }
    }

    public function testMethodsReferringToWrappedDirectory()
    {
        $wrapped   = $this->filesystemSetup();
        $directory = $this->directory($wrapped, ['init']);

        $this->assertSame($wrapped->exists(), $directory->exists());
        $this->assertSame($wrapped->path(), $directory->path());

        $expectedSubdirectory = new TemplateDirectory($wrapped->subdirectory('untracked'), ['init']);
        $this->assertEquals($expectedSubdirectory, $directory->subdirectory('untracked'));
    }

    private function assertFilename(string $expected, File $file): void
    {
        $this->assertSame($expected, $file->name());
    }

    private function directory(FakeDirectory $directory, array $ignoredExt = []): TemplateDirectory
    {
        return new TemplateDirectory($directory, $ignoredExt);
    }

    private function filesystemSetup(): FakeDirectory
    {
        $directory = new FakeDirectory();
        $directory->addFile('foo.txt.sk_file');
        $directory->addFile('bar.txt.sk_init');
        $directory->addFile('other.sk_undefined');

        $subdirectory = $directory->subdirectory('untracked');
        $subdirectory->addFile('baz.txt.sk_local');

        return $directory;
    }
}
