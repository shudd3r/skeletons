<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Files;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Files\File;
use Shudd3r\PackageFiles\Application\FileSystem;


class FileTest extends TestCase
{
    public function testInstantiation()
    {
        $file = $this->file($path);
        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(FileSystem\File::class, $file);
        $this->assertInstanceOf(FileSystem\File::class, $file);
    }

    public function testPathMethod_ReturnsConstructorPath()
    {
        $path = __FILE__;
        $file = $this->file($path);
        $this->assertSame($path, $file->path());
    }

    public function testExistsMethodForNotExistingFilename_ReturnsFalse()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fooBar.txt';
        $this->assertFalse($this->file($path)->exists());
    }

    public function testExistsMethodForExistingFilename_ReturnsTrue()
    {
        $path = __FILE__;
        $this->assertTrue($this->file($path)->exists());
    }

    public function testContentsMethodForNotExistingFile_ReturnsEmptyString()
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'fooBar.txt';
        $this->assertSame('', $this->file($path)->contents());
    }

    public function testContentsMethod_ReturnsFileContents()
    {
        $contents = 'Test file contents...';
        $filename = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filename, $contents);

        $fileContents = $this->file($filename)->contents();
        $this->assertSame($contents, $fileContents);
        unlink($filename);
    }

    public function testWriteMethod_SavesPassedStringInFile()
    {
        $contents = 'Test file contents...';
        $filename = tempnam(sys_get_temp_dir(), 'test');
        $file     = $this->file($filename);

        $file->write($contents);
        $this->assertSame(file_get_contents($filename), $contents);
        $this->assertSame($contents, $file->contents());
        unlink($filename);
    }

    public function testWriteMethodForNotExistingFile_CreatesFileWithPassedStringContent()
    {
        $contents = 'Test file contents...';
        $filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'fooBar.test';
        $file     = $this->file($filename);
        $this->assertFalse($file->exists());

        $file->write($contents);
        $this->assertSame(file_get_contents($filename), $contents);
        $this->assertSame($contents, $file->contents());
        $this->assertTrue($file->exists());
        unlink($filename);
    }

    private function file(?string &$path = null): File
    {
        return new File($path ??= sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.txt');
    }
}
