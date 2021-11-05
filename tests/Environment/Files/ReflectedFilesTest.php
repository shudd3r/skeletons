<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Environment\Files;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Environment\Files\ReflectedFiles;
use Shudd3r\Skeletons\Tests\Doubles;


class ReflectedFilesTest extends TestCase
{
    public function testFileMethod_ReturnsFileFromTargetFiles()
    {
        $target = new Doubles\FakeDirectory();
        $target->addFile('foo.txt');

        $files = new ReflectedFiles($target, new Doubles\FakeDirectory());
        $this->assertSame($target->file('foo.txt'), $files->file('foo.txt'));
    }

    public function testFileList_ReturnsFilesFromTargetThatExistInSource()
    {
        $target = new Doubles\FakeDirectory();
        $target->addFile('target-foo.txt');

        $source = new Doubles\FakeDirectory();
        $source->addFile('source-foo.txt');
        $source->addFile('source-bar.txt');

        $files = new ReflectedFiles($target, $source);
        $expected = [$target->file('source-foo.txt'), $target->file('source-bar.txt')];
        $this->assertEquals($expected, $files->fileList());
    }
}
