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
use Shudd3r\PackageFiles\Files\Directory;
use Shudd3r\PackageFiles\Application\FileSystem;


class DirectoryTest extends TestCase
{
    public function testInstantiation()
    {
        $directory = $this->directory($path);
        $this->assertInstanceOf(Directory::class, $directory);
        $this->assertInstanceOf(FileSystem\Directory::class, $directory);
        $this->assertInstanceOf(FileSystem\Node::class, $directory);
    }

    public function testPath_ReturnsConstructorPath()
    {
        $directory = $this->directory($path);
        $this->assertSame($path, $directory->path());
    }

    /**
     * @dataProvider pathExamples
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testPath_ReturnsNormalizedPath(string $mixedDir, string $normalizedDir)
    {
        $temp      = sys_get_temp_dir();
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
     * @dataProvider pathExamples
     *
     * @param string $mixedDir
     * @param string $normalizedDir
     */
    public function testFileMethod_ReturnsFileWithinDirectory(string $mixedDir, string $normalizedDir)
    {
        $file = $this->directory($path)->file($mixedDir . 'filename.tmp');
        $this->assertInstanceOf(FileSystem\File::class, $file);
        $this->assertSame($path . $normalizedDir . DIRECTORY_SEPARATOR . 'filename.tmp', $file->path());
    }

    public function pathExamples(): array
    {
        return [
            ['\\', ''],
            ['/', ''],
            ['\\Foo/', DIRECTORY_SEPARATOR . 'Foo'],
            ['/Foo/Bar\\', DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar'],
            ['/Foo\\Bar/', DIRECTORY_SEPARATOR . 'Foo' . DIRECTORY_SEPARATOR . 'Bar']
        ];
    }

    private function directory(?string &$path = null): Directory
    {
        return new Directory($path ??= sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tests');
    }
}