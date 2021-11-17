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

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Environment\Files\File\RenamedFile;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;


class RenamedFileTest extends TestCase
{
    public function testNameMethod_ReturnsNameProperty()
    {
        $file = $this->file(new VirtualFile('', 'original/name.ext'), 'given/name.ext');
        $this->assertSame('given/name.ext', $file->name());
    }

    public function testMethodsReferringToWrappedFileInstance()
    {
        $wrapped = new VirtualFile(null);
        $file    = $this->file($wrapped);
        $this->assertEmpty($file->contents());
        $this->assertFalse($file->exists());

        $wrapped->write('contents');
        $this->assertSame('contents', $file->contents());
        $this->assertTrue($file->exists());

        $file->write('new contents');
        $this->assertSame('new contents', $wrapped->contents());

        $file->remove();
        $this->assertFalse($wrapped->exists());
        $this->assertEmpty($wrapped->contents());
    }

    private function file(VirtualFile $wrapped, string $name = 'foo.txt'): RenamedFile
    {
        return new RenamedFile($wrapped, $name);
    }
}
