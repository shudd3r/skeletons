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

use Shudd3r\Skeletons\Tests\Environment\Files\LocalFileSystemTests;


class LocalDirectoryTest extends LocalFileSystemTests
{
    public function test_path_method_returns_path_property(): void
    {
        $this->assertSame(self::$root . DIRECTORY_SEPARATOR . 'test', self::directory('test')->path());
    }

    /** @dataProvider directoryPathNormalizations */
    public function test_returned_path_is_normalized(string $mixedDir, string $normalizedDir): void
    {
        $this->assertEquals(self::directory($normalizedDir, true), $directory = self::directory($mixedDir, true));
        $this->assertSame($normalizedDir, $directory->path());
    }

    public function test_exists_method(): void
    {
        $this->assertTrue(self::directory()->exists());
        $this->assertFalse(self::directory('foo/bar')->exists());
    }

    public function test_exists_method_for_file_path_returns_false(): void
    {
        self::create('foo/bar.dir/baz.tmp');
        $this->assertTrue(self::directory('foo/bar.dir')->exists());
        $this->assertFalse(self::directory('foo/bar.dir/baz.tmp')->exists());
        self::clear();
    }

    public function test_file_method_creates_file_instance(): void
    {
        self::create('exists.tmp');
        $directory = self::directory();
        $this->assertTrue($directory->file('exists.tmp')->exists());
        $this->assertFalse($directory->file('notExists.tmp')->exists());
        self::clear();
    }

    public function test_file_method_ignores_superfluous_slashes(): void
    {
        $directory = self::directory();
        $file      = self::file('dir/path/file.tmp');
        $this->assertEquals($directory->file('dir/path/file.tmp/'), $file);
        $this->assertEquals($directory->file('\dir\path\file.tmp'), $file);
    }

    public function test_file_list_method_returns_files_array(): void
    {
        $directory = self::directory();
        $files     = ['a.tmp', 'b.tmp', 'c.tmp'];
        array_walk($files, fn($file) => self::create($file));

        $this->assertEquals(self::files($files), $directory->fileList());
        self::clear();
    }

    public function test_file_structure(): void
    {
        $directory = self::directory();
        $files = ['b.tmp', 'a.tmp', 'foo/c.tmp', 'foo/d.tmp', 'bar/e.tmp', 'foo/baz/f.tmp'];
        array_walk($files, fn($file) => self::create($file));

        $expected = self::files(['a.tmp', 'b.tmp', 'bar/e.tmp', 'foo/c.tmp', 'foo/d.tmp', 'foo/baz/f.tmp']);
        $this->assertEquals($expected, $directory->fileList());

        $expected = self::files(['c.tmp', 'd.tmp', 'baz/f.tmp'], 'foo');
        $this->assertEquals($expected, $directory->subdirectory('foo')->fileList());

        $this->assertEquals(self::files(['e.tmp'], 'bar'), $directory->subdirectory('bar')->fileList());
        $this->assertEquals(self::files(['f.tmp'], 'foo/baz'), $directory->subdirectory('foo/baz')->fileList());
        self::clear();
    }

    public static function directoryPathNormalizations(): iterable
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
