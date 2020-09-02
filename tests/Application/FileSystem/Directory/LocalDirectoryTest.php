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
use Shudd3r\PackageFiles\Application\FileSystem\Directory\LocalDirectory;
use Shudd3r\PackageFiles\Application\FileSystem\File\LocalFile;
use Shudd3r\PackageFiles\Application\FileSystem;


class LocalDirectoryTest extends TestCase
{
    protected static string $testDirectory;

    public static function setUpBeforeClass(): void
    {
        self::$testDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tests';
        mkdir(self::$testDirectory);
        mkdir($subDir = self::$testDirectory . DIRECTORY_SEPARATOR . 'first');
        mkdir(self::$testDirectory . DIRECTORY_SEPARATOR . 'second');
        file_put_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'one.tmp', '1');
        file_put_contents(self::$testDirectory . DIRECTORY_SEPARATOR . 'two.tmp', '2');
        file_put_contents($subDir . DIRECTORY_SEPARATOR . 'sub.tmp', '3');
    }

    public static function tearDownAfterClass(): void
    {
        self::removeDirectory(self::$testDirectory);
    }

    public function testInstantiation()
    {
        $directory = $this->directory($path);
        $this->assertInstanceOf(LocalDirectory::class, $directory);
        $this->assertInstanceOf(FileSystem\Directory::class, $directory);
        $this->assertInstanceOf(FileSystem\Node::class, $directory);
    }

    public function testPath_ReturnsConstructorPath()
    {
        $directory = $this->directory($path);
        $this->assertSame($path, $directory->path());
    }

    /**
     * @dataProvider pathNormalizations
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testPath_ReturnsNormalizedPath(string $mixedDir, string $normalizedDir)
    {
        $temp      = self::$testDirectory;
        $path      = $temp . $mixedDir;
        $directory = $this->directory($path);

        $this->assertSame($temp . $normalizedDir, $directory->path());
    }

    public function testExistsMethod_ReturnsTrueForExistingDirectory()
    {
        $path = __DIR__;
        $this->assertTrue($this->directory($path)->exists());
    }

    public function testExistsMethod_ReturnsFalseForNotExistingDirectory()
    {
        $path = __DIR__ . '/foo/bar/baz';
        $this->assertFalse($this->directory($path)->exists());
    }

    /**
     * @dataProvider pathNormalizations
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testFileMethod_ReturnsFileWithinDirectory(string $mixedDir, string $normalizedDir)
    {
        $directory = $this->directory($path);
        $expected  = new LocalFile($directory->path() . $normalizedDir . DIRECTORY_SEPARATOR . 'filename.tmp');
        $this->assertEquals($expected, $directory->file($mixedDir . 'filename.tmp'));
    }

    public function testFileMethod_ReturnsBothExistingAndNotExistingFile()
    {
        $path      = __DIR__;
        $directory = $this->directory($path);
        $this->assertFalse($directory->file('notExistingFile.txt')->exists());
        $this->assertTrue($directory->file(basename(__FILE__))->exists());
    }

    public function testFilesMethod_ReturnsArrayOfExistingFiles()
    {
        $directory = $this->directory();

        $expected = [
            new LocalFile(self::$testDirectory . DIRECTORY_SEPARATOR . 'one.tmp'),
            new LocalFile(self::$testDirectory . DIRECTORY_SEPARATOR . 'two.tmp'),
        ];
        $this->assertEquals($expected, $directory->files());
    }

    public function testSubdirectoriesMethod_ReturnsArrayOfSubdirectories()
    {
        $directory = $this->directory();

        $expected = [
            new LocalDirectory(self::$testDirectory . DIRECTORY_SEPARATOR . 'first'),
            new LocalDirectory(self::$testDirectory . DIRECTORY_SEPARATOR . 'second')
        ];
        $this->assertEquals($expected, $directory->subdirectories());
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

    private function directory(?string &$path = null): LocalDirectory
    {
        return new LocalDirectory($path ??= self::$testDirectory);
    }

    private static function removeDirectory(string $path): void
    {
        if (strpos($path, self::$testDirectory) !== 0) {
            return;
        }

        if (is_file($path)) {
            unlink($path);
            return;
        }

        array_map(fn($path) => self::removeDirectory($path), glob($path . DIRECTORY_SEPARATOR . '*'));
        rmdir($path);
    }
}
