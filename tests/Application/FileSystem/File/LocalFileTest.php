<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\FileSystem\File;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\FileSystem;
use Shudd3r\PackageFiles\Tests\Application\FileSystem\LocalFileSystemMethods;


class LocalFileTest extends TestCase
{
    use LocalFileSystemMethods;

    public function testInstantiation()
    {
        $file = self::file('test.tmp');
        $this->assertInstanceOf(FileSystem\File\LocalFile::class, $file);
        $this->assertInstanceOf(FileSystem\File::class, $file);
        $this->assertInstanceOf(FileSystem\File::class, $file);
    }

    public function testPathMethod_ReturnsConstructorPath()
    {
        $file = new FileSystem\File\LocalFile($path = self::$root . DIRECTORY_SEPARATOR . 'test.tmp');
        $this->assertSame($path, $file->path());
    }

    public function testExistsMethod()
    {
        $file = self::file('test.tmp');
        $this->assertFalse($file->exists());
        self::create('test.tmp');
        $this->assertTrue($file->exists());
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
        $this->assertSame($contents, file_get_contents($file->path()));
        self::clear();
    }

    public function testForNotExistingFile_WriteMethod_CreatesFileWithPassedStringContent()
    {
        $file = self::file('test.tmp');
        $this->assertFalse($file->exists());

        $file->write($contents = 'Test file contents...');

        $this->assertTrue($file->exists());
        $this->assertSame($contents, file_get_contents($file->path()));
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
}
