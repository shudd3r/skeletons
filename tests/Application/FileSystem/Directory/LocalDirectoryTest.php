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
use Shudd3r\PackageFiles\Application\FileSystem;
use Shudd3r\PackageFiles\Tests\Application\FileSystem\LocalFileSystemMethods;


class LocalDirectoryTest extends TestCase
{
    use LocalFileSystemMethods;

    public function testInstantiation()
    {
        $directory = self::directory();
        $this->assertInstanceOf(FileSystem\Directory\LocalDirectory::class, $directory);
        $this->assertInstanceOf(FileSystem\Directory::class, $directory);
        $this->assertInstanceOf(FileSystem\Node::class, $directory);
    }

    public function testPath_ReturnsConstructorPath()
    {
        $this->assertSame(__DIR__, self::directory(__DIR__)->path());
    }

    /**
     * @dataProvider pathNormalizations
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testPath_ReturnsNormalizedPath(string $mixedDir, string $normalizedDir)
    {
        $this->assertSame(self::$root . $normalizedDir, self::directory(self::$root . $mixedDir)->path());
    }

    public function testExistsMethod()
    {
        $this->assertTrue(self::directory(__DIR__)->exists());
        $this->assertFalse(self::directory(__DIR__ . '/foo/bar/baz')->exists());
    }

    /**
     * @dataProvider pathNormalizations
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testFileMethod_ReturnsFileWithNormalizedDirectoryPath(string $mixedDir, string $normalizedDir)
    {
        $directory = self::directory();
        $filename  = $directory->path() . $normalizedDir . DIRECTORY_SEPARATOR . 'filename.tmp';
        $this->assertEquals(self::file($filename), $directory->file($mixedDir . 'filename.tmp'));
    }

    public function testFileMethod_ReturnsBothExistingAndNotExistingFile()
    {
        $directory = self::directory(__DIR__);
        $this->assertFalse($directory->file('notExistingFile.txt')->exists());
        $this->assertTrue($directory->file(basename(__FILE__))->exists());
    }

    public function testFilesMethod_ReturnsArrayOfExistingFiles()
    {
        $directory = self::directory();
        $this->assertEmpty($directory->files());

        $this->create('one.tmp');
        $this->create('two.tmp');

        $this->assertEquals(self::files(['one.tmp', 'two.tmp']), $directory->files());

        self::remove();
    }

    public function testSubdirectoriesMethod_ReturnsArrayOfExistingSubdirectories()
    {
        self::create('first/a.tmp');
        self::create('second/a.tmp');

        $this->assertEquals(self::directories(['first', 'second']), self::directory()->subdirectories());

        self::remove();
    }

    public function testFileStructure()
    {
        $root = self::directory();

        $this->assertEmpty($root->files());
        $this->assertEmpty($root->subdirectories());

        $filenames = ['b.tmp', 'a.tmp', 'foo/c.tmp', 'foo/d.tmp', 'bar/e.tmp', 'foo/baz/f.tmp'];
        $files     = self::files($filenames);

        $this->assertEmpty($root->files());
        $this->assertEmpty($root->subdirectories());

        foreach ($files as $file) {
            $this->assertFalse($file->exists());
            $file->write('x');
            $this->assertTrue($file->exists());
        }

        $this->assertEquals(self::files(['a.tmp', 'b.tmp']), $root->files());
        $this->assertEquals(self::files(['foo/c.tmp', 'foo/d.tmp']), $root->subdirectory('foo')->files());
        $this->assertEquals(self::files(['bar/e.tmp']), $root->subdirectory('bar')->files());
        $this->assertEquals(self::files(['foo/baz/f.tmp']), $root->subdirectory('foo/baz')->files());

        $this->assertEquals(self::directories(['bar', 'foo']), $root->subdirectories());
        $this->assertEquals(self::directories(['foo/baz']), $root->subdirectory('foo')->subdirectories());

        self::remove();
    }

    public function pathNormalizations(): array
    {
        return [
            ['\\', ''],
            ['/', ''],
            ['\\Foo/', DIRECTORY_SEPARATOR . 'Foo'],
            ['/Foo/Bar\\', DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar'],
            ['/Foo\\Bar/', DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar']
        ];
    }
}
