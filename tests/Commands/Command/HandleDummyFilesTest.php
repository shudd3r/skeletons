<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Commands\Command;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Commands\Command;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Tests\Doubles;


class HandleDummyFilesTest extends TestCase
{
    private static Doubles\MockedTerminal $terminal;

    public static function setUpBeforeClass(): void
    {
        self::$terminal = new Doubles\MockedTerminal();
    }

    public function testDummyInRootDirectory_IsIgnored()
    {
        $directory = $this->directory();
        $dummies   = $this->directory(['.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertCount(0, $directory->fileList());
        $this->assertEmpty(self::$terminal->messagesSent());
    }

    public function testNonTemplateDummies_AreIgnored()
    {
        $directory = $this->directory(['foo/.gitkeep', 'bar/.gitkeep', 'bar/file.txt']);
        $dummies   = $this->directory(['foo/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertCount(3, $directory->fileList());
        $this->assertEmpty(self::$terminal->messagesSent());
    }

    public function testDummiesWithOriginalFileInSameDirectory_AreRemovedAndFileListIsSent()
    {
        $directory = $this->directory(['foo/.gitkeep', 'foo/file1.txt', 'bar/.gitkeep', 'bar/file2.txt']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $messageStream = implode(',', self::$terminal->messagesSent());
        $this->assertStringContainsString('foo/.gitkeep', $messageStream);
        $this->assertStringContainsString('bar/.gitkeep', $messageStream);
        $this->assertFiles($directory, ['foo/file1.txt', 'bar/file2.txt']);
    }

    public function testDummiesWithOriginalFilesInSubdirectories_AreRemoved()
    {
        $directory = $this->directory(['foo/.gitkeep', 'foo/bar/file.txt', 'bar/.gitkeep']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertFiles($directory, ['foo/bar/file.txt', 'bar/.gitkeep']);
    }

    public function testMissingDummyFiles_AreCreated()
    {
        $directory = $this->directory($files = ['foo/orig0.txt', 'bar/orig1.txt', 'bar/baz/orig2,txt']);
        $dummies   = $this->directory(['foo/bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertFiles($directory, array_merge($files, ['foo/bar/.gitkeep']));
    }

    public function testHandlingBothRedundantAndMissingDummies()
    {
        $directory = $this->directory(['foo/file1.txt', 'foo/.gitkeep', 'bar/file2.txt']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/baz/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertFiles($directory, ['foo/file1.txt', 'bar/file2.txt', 'bar/baz/.gitkeep']);
    }

    private function assertFiles(Files\Directory $directory, array $filenames)
    {
        $expected = array_flip($filenames);
        foreach ($directory->fileList() as $file) {
            $filename = $file->name();
            $this->assertArrayHasKey($filename, $expected);
            unset($expected[$filename]);
        }

        $this->assertSame([], $expected);
    }

    private function directory(array $filenames = []): Files\Directory
    {
        $directory = new Files\Directory\VirtualDirectory();
        foreach ($filenames as $filename) {
            $directory->addFile($filename);
        }

        return $directory;
    }

    private function command(Files\Directory $directory, Files $files): Command
    {
        return new Command\HandleDummyFiles($directory, $files, self::$terminal->reset());
    }
}
