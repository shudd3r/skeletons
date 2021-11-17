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
use Shudd3r\Skeletons\Environment\Files\IndexedFiles;
use Shudd3r\Skeletons\Environment\Files\File\RenamedFile;
use Shudd3r\Skeletons\Environment\Files\Directory;


class IndexedFilesTest extends TestCase
{
    public function testFileMethod_ForChangedIndexName_ReturnsRenamedFile()
    {
        $source = $this->sourceFiles(['foo.txt']);
        $files  = new IndexedFiles($source, ['bar.txt' => 'foo.txt']);

        $expected = new RenamedFile($source->file('foo.txt'), 'bar.txt');
        $this->assertEquals($expected, $files->file('bar.txt'));
    }

    public function testFileMethod_ForUnchangedIndexName_ReturnsSourceFile()
    {
        $source = $this->sourceFiles(['foo.txt']);
        $files  = new IndexedFiles($source, ['foo.txt' => 'foo.txt']);

        $expected = $source->file('foo.txt');
        $this->assertSame($expected, $files->file('foo.txt'));
    }

    public function testFileMethod_ForNotIndexedName_ReturnsSourceFile()
    {
        $source = $this->sourceFiles(['foo.txt']);
        $files = new IndexedFiles($source, []);

        $expected = $source->file('foo.txt');
        $this->assertSame($expected, $files->file('foo.txt'));
    }

    public function testFileListMethod_ReturnsOnlyIndexedFiles()
    {
        $source = $this->sourceFiles(['dir/foo.txt', 'bar.ini', 'baz.out']);
        $index  = [
            'foo.txt' => 'dir/foo.txt',
            'bar.ini' => 'bar.ini'
        ];
        $files = new IndexedFiles($source, $index);

        $expected = [
            new RenamedFile($source->file('dir/foo.txt'), 'foo.txt'),
            $source->file('bar.ini')
        ];
        $this->assertEquals($expected, $files->fileList());
    }

    private function sourceFiles(array $filenames): Directory
    {
        $source = new Directory\VirtualDirectory();
        array_walk($filenames, fn (string $filename) => $source->addFile($filename));
        return $source;
    }
}
