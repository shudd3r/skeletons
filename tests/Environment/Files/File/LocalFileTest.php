<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Environment\Files\File;

use Shudd3r\Skeletons\Tests\Environment\Files\LocalFileSystemTests;
use Shudd3r\Skeletons\Environment\Files;


class LocalFileTest extends LocalFileSystemTests
{
    public function test_instantiation(): void
    {
        $file = new Files\File\LocalFile(self::directory(), 'test.tmp');
        $this->assertEquals(self::file('test.tmp'), $file);
        $this->assertInstanceOf(Files\File::class, $file);
    }

    public function test_path_method_returns_path_property(): void
    {
        $this->assertSame('test.tmp', self::file('test.tmp')->name());
    }

    /** @dataProvider filePathNormalizations */
    public function test_path_is_normalized(string $mixedFilename, string $normalizedFilename): void
    {
        $this->assertEquals(self::file($normalizedFilename), $file = self::file($mixedFilename));
        $this->assertSame($normalizedFilename, $file->name());
    }

    public static function filePathNormalizations(): array
    {
        return [
            ['file\\', "file"],
            ['file.tmp/', "file.tmp"],
            ['\\\\Foo.tmp/file/', "Foo.tmp/file"],
            ['/Foo/Bar\\baz.tmp\\', "Foo/Bar/baz.tmp"],
            ['/Foo\\Bar/file.tmp', "Foo/Bar/file.tmp"]
        ];
    }

    public function test_exists_method(): void
    {
        $file = self::file('test.tmp');
        $this->assertFalse($file->exists());
        self::create('test.tmp');
        $this->assertTrue($file->exists());
        self::clear();
    }

    public function test_exists_method_for_directory_path_returns_false(): void
    {
        self::create('foo/bar.dir/baz.tmp');
        $this->assertFalse(self::file('foo/bar.dir')->exists());
        $this->assertTrue(self::file('foo/bar.dir/baz.tmp')->exists());
        self::clear();
    }

    public function test_for_not_existing_file_contents_method_returns_empty_string(): void
    {
        $this->assertSame('', self::file('test.tmp')->contents());
    }

    public function test_for_existing_file_contents_method_returns_file_contents(): void
    {
        self::create('test.tmp', $contents = 'Test file contents...');
        $this->assertSame($contents, self::file('test.tmp')->contents());
        self::clear();
    }

    public function test_write_method_saves_passed_string(): void
    {
        self::create('test.tmp', 'Initial file contents...');
        $file = self::file('test.tmp');

        $file->write($contents = 'Written file contents...');
        $this->assertSame($contents, $file->contents());
        $this->assertSame($contents, file_get_contents(self::$root . DIRECTORY_SEPARATOR . $file->name()));
        self::clear();
    }

    public function test_for_not_existing_file_write_method_creates_file_with_passed_contents(): void
    {
        $file = self::file('test.tmp');
        $this->assertFalse($file->exists());

        $file->write($contents = 'Test file contents...');

        $this->assertTrue($file->exists());
        $this->assertSame($contents, file_get_contents(self::$root . DIRECTORY_SEPARATOR . $file->name()));
        $this->assertSame($contents, $file->contents());
        self::clear();
    }

    public function test_for_not_existing_file_write_method_creates_required_directory_structure(): void
    {
        $file = self::file('missing/directory/file.tmp');
        $this->assertFalse($file->exists());

        $file->write('Test file contents...');
        $this->assertTrue($file->exists());
        self::clear();
    }

    public function test_remove_method_deletes_file(): void
    {
        self::create('test.tmp', 'contents');
        $file = self::file('test.tmp');
        $this->assertSame('contents', $file->contents());
        $this->assertTrue($file->exists());

        $file->remove();

        $this->assertFalse($file->exists());
        $this->assertEmpty($file->contents());
        self::clear();
    }
}
