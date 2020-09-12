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

use Shudd3r\PackageFiles\Tests\Application\FileSystemTests;
use Shudd3r\PackageFiles\Application\FileSystem;


class LocalDirectoryTest extends FileSystemTests
{
    public function testInstantiation()
    {
        $directory = new FileSystem\Directory\LocalDirectory(self::$root . DIRECTORY_SEPARATOR . 'test');
        $this->assertEquals(self::directory('test'), $directory);
        $this->assertInstanceOf(FileSystem\Directory\LocalDirectory::class, $directory);
        $this->assertInstanceOf(FileSystem\Directory::class, $directory);
        $this->assertInstanceOf(FileSystem\Node::class, $directory);
    }

    public function testPathMethod_ReturnsPathProperty()
    {
        $this->assertSame(self::$root . DIRECTORY_SEPARATOR . 'test', self::directory('test')->path());
    }

    /**
     * @dataProvider pathNormalizations
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testPathIsNormalized(string $mixedDir, string $normalizedDir)
    {
        $this->assertEquals(self::directory($normalizedDir, true), $directory = self::directory($mixedDir, true));
        $this->assertSame($normalizedDir, $directory->path());
    }

    public function testRelativePath()
    {
        $path      = 'foo' . DIRECTORY_SEPARATOR . 'bar' . DIRECTORY_SEPARATOR . 'baz';
        $root      = self::directory();
        $directory = $root->subdirectory($path);

        $this->assertSame($path, $directory->pathRelativeTo($root));
        $this->assertSame($directory->path(), $directory->pathRelativeTo(self::directory(__DIR__, true)));
    }

    public function testExistsMethod()
    {
        $this->assertTrue(self::directory()->exists());
        $this->assertFalse(self::directory('foo/bar')->exists());
    }

    public function testExistsMethodForFilePath_ReturnsFalse()
    {
        self::create('foo/bar.dir/baz.tmp');
        $this->assertTrue(self::directory('foo/bar.dir')->exists());
        $this->assertFalse(self::directory('foo/bar.dir/baz.tmp')->exists());
        self::clear();
    }

    public function testCreateMethod()
    {
        $directory = self::directory('new/directory');
        $this->assertFalse($directory->exists());
        $this->assertEquals(self::directory('new/directory'), $directory);

        $directory->create();
        $this->assertTrue($directory->exists());
        $this->assertTrue(self::directory('new')->exists());
        $this->assertEquals(self::directory('new/directory'), $directory);

        $directory->create();
        $this->assertTrue($directory->exists());
        $this->assertEquals(self::directory('new/directory'), $directory);
        self::clear();
    }

    public function testFileMethod()
    {
        self::create('exists.tmp');
        $directory = self::directory();
        $this->assertTrue($directory->file('exists.tmp')->exists());
        $this->assertFalse($directory->file('notExists.tmp')->exists());
        self::clear();
    }

    public function testFileMethod_RemovesSuperfluousSlashes()
    {
        $directory = self::directory();
        $file      = self::file('dir/path/file.tmp');
        $this->assertEquals($directory->file('dir/path/file.tmp/'), $file);
        $this->assertEquals($directory->file('\dir\path\file.tmp'), $file);
    }

    public function testFilesMethod_ReturnsArrayOfExistingFilesInAlphabeticalOrder()
    {
        $directory = self::directory();
        $this->assertEmpty($directory->files());

        $files = ['a.tmp', 'b.tmp', 'c.tmp'];
        array_walk($files, fn($file) => self::create($file));
        $this->assertEquals(self::files($files), $directory->files());
        self::clear();
    }

    public function testSubdirectoriesMethod_ReturnsArrayOfExistingSubdirectories()
    {
        $directory = self::directory();
        $this->assertEmpty($directory->subdirectories());

        $directories = ['a', 'b', 'c'];
        array_walk($directories, fn($dir) => self::create($dir . '/file.tmp'));
        $this->assertEquals(self::directories($directories), $directory->subdirectories());
        self::clear();
    }

    public function testFileStructure()
    {
        $directory = self::directory();
        $files = ['b.tmp', 'a.tmp', 'foo/c.tmp', 'foo/d.tmp', 'bar/e.tmp', 'foo/baz/f.tmp'];
        array_walk($files, fn($file) => self::create($file));

        $this->assertEquals(self::files(['a.tmp', 'b.tmp']), $directory->files());
        $this->assertEquals(self::files(['foo/c.tmp', 'foo/d.tmp']), $directory->subdirectory('foo')->files());
        $this->assertEquals(self::files(['bar/e.tmp']), $directory->subdirectory('bar')->files());
        $this->assertEquals(self::files(['foo/baz/f.tmp']), $directory->subdirectory('foo/baz')->files());
        $this->assertEquals(self::directories(['bar', 'foo']), $directory->subdirectories());
        $this->assertEquals(self::directories(['foo/baz']), $directory->subdirectory('foo')->subdirectories());
        self::clear();
    }

    public function pathNormalizations(): array
    {
        $ds = DIRECTORY_SEPARATOR;
        return [
            ['\\', ''],
            ['/', ''],
            ['//Foo/', "{$ds}{$ds}Foo"],
            ['\Foo/Bar\\', "{$ds}Foo{$ds}Bar"],
            ['Foo\\Bar/baz////', "Foo{$ds}Bar{$ds}baz"]
        ];
    }
}
