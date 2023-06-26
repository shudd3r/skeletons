<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Templates;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Templates\DummyFiles;
use Shudd3r\Skeletons\Environment\Files;


class DummyFilesTest extends TestCase
{
    public function testDummyInRootDirectory_IsIgnored()
    {
        $dummies = $this->dummies(['.gitkeep']);

        $verified = $dummies->verifiedFiles($this->directory());
        $this->assertEmpty($verified->missingFiles());
        $this->assertEmpty($verified->redundantFiles());

        $verified = $dummies->verifiedFiles($this->directory(['root.file', '.gitkeep']));
        $this->assertEmpty($verified->missingFiles());
        $this->assertEmpty($verified->redundantFiles());
    }

    public function testNonTemplateDummies_AreIgnored()
    {
        $dummies = $this->dummies([]);

        $verified = $dummies->verifiedFiles($this->directory(['bar/.gitkeep', 'bar/file.txt']));
        $this->assertEmpty($verified->missingFiles());
        $this->assertEmpty($verified->redundantFiles());
    }

    public function testSynchronizedDirectory()
    {
        $dummies = $this->dummies(['foo/.gitkeep', 'bar/baz/.gitkeep']);

        $verified = $dummies->verifiedFiles($this->directory(['foo/bar/exists.foo', 'bar/baz/.gitkeep']));
        $this->assertEmpty($verified->missingFiles());
        $this->assertEmpty($verified->redundantFiles());
    }

    public function testMissingSubdirectories()
    {
        $dummies = $this->dummies(['foo/bar/.gitkeep', 'baz/.gitkeep']);

        $verified = $dummies->verifiedFiles($this->directory(['foo/exists.foo']));
        $this->assertFiles(['foo/bar/.gitkeep', 'baz/.gitkeep'], $verified->missingFiles());
        $this->assertEmpty($verified->redundantFiles());
    }

    public function testDummiesInExistingDirectories()
    {
        $dummies = $this->dummies(['foo/.gitkeep', 'bar/.gitkeep']);

        $package  = $this->directory(['foo/bar/baz/exists.foo', 'bar/exists.bar', 'foo/.gitkeep', 'bar/.gitkeep']);
        $verified = $dummies->verifiedFiles($package);
        $this->assertEmpty($verified->missingFiles());
        $this->assertFiles(['foo/.gitkeep', 'bar/.gitkeep'], $verified->redundantFiles());
    }

    public function testBothMissingAndRedundantDummies()
    {
        $dummies = $this->dummies(['foo/bar/.gitkeep', 'bar/baz/.gitkeep']);

        $package  = $this->directory(['foo/baz/missing.bar', 'bar/baz/exists.baz', 'bar/baz/.gitkeep']);
        $verified = $dummies->verifiedFiles($package);
        $this->assertfiles(['foo/bar/.gitkeep'], $verified->missingFiles());
        $this->assertFiles(['bar/baz/.gitkeep'], $verified->redundantFiles());
    }

    private function assertFiles(array $filenames, array $fileList): void
    {
        $getFilename = fn (Files\File $file) => $file->name();
        $this->assertEquals($filenames, array_map($getFilename, $fileList));
    }

    private function directory(array $filenames = []): Files\Directory
    {
        $directory = new Files\Directory\VirtualDirectory();
        foreach ($filenames as $filename) {
            $directory->addFile($filename);
        }

        return $directory;
    }

    private function dummies(array $filenames): DummyFiles
    {
        return new DummyFiles($this->directory($filenames));
    }
}
