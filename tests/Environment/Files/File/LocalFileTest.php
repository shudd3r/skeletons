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
    public function testInstantiation()
    {
        $file = new Files\File\LocalFile(self::directory(), 'test.tmp');
        $this->assertEquals(self::file('test.tmp'), $file);
        $this->assertInstanceOf(Files\File::class, $file);
    }

    public function testPathMethod_ReturnsPathProperty()
    {
        $this->assertSame('test.tmp', self::file('test.tmp')->name());
    }

    /** @dataProvider filePathNormalizations */
    public function testPathIsNormalized(string $mixedFilename, string $normalizedFilename)
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

    public function testExistsMethod()
    {
        $file = self::file('test.tmp');
        $this->assertFalse($file->exists());
        self::create('test.tmp');
        $this->assertTrue($file->exists());
        self::clear();
    }

    public function testExistsMethodForDirectoryPath_ReturnsFalse()
    {
        self::create('foo/bar.dir/baz.tmp');
        $this->assertFalse(self::file('foo/bar.dir')->exists());
        $this->assertTrue(self::file('foo/bar.dir/baz.tmp')->exists());
        self::clear();
    }

    public function testForNotExistingFile_ContentsMethod_ReturnsEmptyString()
    {
        $this->assertSame('', self::file('test.tmp')->contents());
    }

    public function testForExistingFile_ContentsMethod_ReturnsFileContents()
    {
        self::create('test.tmp', $contents = 'Test file contents...');
        $this->assertSame($contents, self::file('test.tmp')->contents());
        self::clear();
    }

    public function testWriteMethod_SavesPassedStringInFile()
    {
        self::create('test.tmp', 'Initial file contents...');
        $file = self::file('test.tmp');

        $file->write($contents = 'Written file contents...');
        $this->assertSame($contents, $file->contents());
        $this->assertSame($contents, file_get_contents(self::$root . DIRECTORY_SEPARATOR . $file->name()));
        self::clear();
    }

    public function testForNotExistingFile_WriteMethod_CreatesFileWithPassedContents()
    {
        $file = self::file('test.tmp');
        $this->assertFalse($file->exists());

        $file->write($contents = 'Test file contents...');

        $this->assertTrue($file->exists());
        $this->assertSame($contents, file_get_contents(self::$root . DIRECTORY_SEPARATOR . $file->name()));
        $this->assertSame($contents, $file->contents());
        self::clear();
    }

    public function testForNotExistingFile_WriteMethod_CreatesRequiredDirectoryStructure()
    {
        $file = self::file('missing/directory/file.tmp');
        $this->assertFalse($file->exists());

        $file->write('Test file contents...');
        $this->assertTrue($file->exists());
        self::clear();
    }

    public function testRemoveMethod_RemovesFile()
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
