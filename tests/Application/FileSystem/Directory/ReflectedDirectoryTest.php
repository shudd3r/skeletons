<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\FileSystem\Directory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\FileSystem\Directory\ReflectedDirectory;
use Shudd3r\PackageFiles\Application\FileSystem\DirectoryFiles;
use Shudd3r\PackageFiles\Tests\Doubles\FakeDirectory;


class ReflectedDirectoryTest extends TestCase
{
    public function testFiles_ReturnsFilesExistingInOriginDirectoryWithRootDirectoryPaths()
    {
        $root      = new FakeDirectory('root/path');
        $origin    = new FakeDirectory('origin/path');
        $reflected = new ReflectedDirectory($root, $origin);

        $origin->addFile('foo.txt');
        $origin->addFile('foo/bar.txt');

        $this->assertEquals(new DirectoryFiles([]), $root->files());
        $expected = new DirectoryFiles([$root->file('foo.txt'), $root->file('foo/bar.txt')]);
        $this->assertEquals($expected, $reflected->files());
    }

    public function testPath_ReturnsRootPath()
    {
        $root      = new FakeDirectory('/root/path');
        $reflected = new ReflectedDirectory($root, new FakeDirectory('/origin/path'));
        $this->assertSame($root->path(), $reflected->path());
    }

    public function testExists_ReturnsFromRootDirectory()
    {
        $root      = new FakeDirectory('/root/path');
        $reflected = new ReflectedDirectory($root, new FakeDirectory('/origin/path', false));

        $root->exists = true;
        $this->assertTrue($reflected->exists());

        $root->exists = false;
        $this->assertFalse($reflected->exists());
    }

    public function testSubdirectory_ReturnsReflectedSubdirectory()
    {
        $root      = new FakeDirectory('root');
        $origin    = new FakeDirectory('origin');
        $reflected = new ReflectedDirectory($root, $origin);

        $expected = new ReflectedDirectory($root->subdirectory('foo'), $origin->subdirectory('foo'));
        $this->assertEquals($expected, $reflected->subdirectory('foo'));
    }

    public function testFile_ReturnsFromRootDirectory()
    {
        $root      = new FakeDirectory('root/directory');
        $reflected = new ReflectedDirectory($root, new FakeDirectory('origin/directory'));

        $root->addFile('foo.txt');
        $this->assertSame($root->file('foo.txt'), $reflected->file('foo.txt'));
    }
}
