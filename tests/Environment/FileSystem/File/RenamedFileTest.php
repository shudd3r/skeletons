<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Environment\FileSystem\File;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Environment\FileSystem\File\RenamedFile;
use Shudd3r\Skeletons\Tests\Doubles\MockedFile;


class RenamedFileTest extends TestCase
{
    public function testNameMethod_ReturnsNameProperty()
    {
        $file = $this->file(new MockedFile('', 'original/name.ext'), 'given/name.ext');
        $this->assertSame('given/name.ext', $file->name());
    }

    public function testMethodsReferringToWrappedFileInstance()
    {
        $wrapped = new MockedFile(null);
        $file    = $this->file($wrapped);
        $this->assertSame('', $file->contents());
        $this->assertFalse($file->exists());

        $wrapped->write('contents');
        $this->assertSame('contents', $file->contents());
        $this->assertTrue($file->exists());

        $file->write('new contents');
        $this->assertSame('new contents', $wrapped->contents());
    }

    private function file(MockedFile $wrapped, string $name = 'foo.txt'): RenamedFile
    {
        return new RenamedFile($wrapped, $name);
    }
}
