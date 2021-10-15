<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Environment\FileSystem\Directory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory\CachedDirectory;
use Shudd3r\PackageFiles\Tests\Doubles\FakeDirectory;


class CachedDirectoryTest extends TestCase
{
    public function testFiles_ReturnsMemoizedOriginDirectoryFiles()
    {
        $origin = new FakeDirectory('origin/path');
        $cached = new CachedDirectory($origin);

        $origin->addFile('foo/some.file');
        $origin->addFile('bar.txt');

        $this->assertEquals($origin->files(), $cachedFiles = $cached->files());

        $origin->addFile('baz.file');
        $this->assertNotEquals($origin->files(), $cached->files());
        $this->assertSame($cachedFiles, $cached->files());
    }

    public function testSubdirectory_ReturnsCachedOriginSubdirectory()
    {
        $origin = new FakeDirectory('origin/path');
        $cached = new CachedDirectory($origin);

        $this->assertEquals(new CachedDirectory($origin->subdirectory('foo')), $cached->subdirectory('foo'));
    }

    public function testMethodsForwardedToOriginDirectory()
    {
        $origin = new FakeDirectory('origin/path', true);
        $cached = new CachedDirectory($origin);

        $this->assertSame($origin->path(), $cached->path());
        $this->assertTrue($cached->exists());

        $origin->addFile('foo/bar.txt');
        $this->assertSame($origin->file('foo/bar.txt'), $cached->file('foo/bar.txt'));

        $cached = new CachedDirectory(new FakeDirectory('origin/path', false));
        $this->assertFalse($cached->exists());
    }
}
