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
use Shudd3r\Skeletons\Templates\DummyFiles;
use Shudd3r\Skeletons\Tests\Doubles;


class HandleDummyFilesTest extends TestCase
{
    private static Doubles\MockedTerminal $terminal;

    public static function setUpBeforeClass(): void
    {
        self::$terminal = new Doubles\MockedTerminal();
    }

    public function testWithoutRedundantOrMissingDummyFiles_CommandExecutesWithoutSideEffects()
    {
        $directory = $this->directory($files = ['root.txt', 'foo/file.txt', 'bar/.gitkeep']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertFiles($files, $directory);
        $this->assertMessages();
    }

    public function testMissingDummyFiles_AreCreatedWithTemplateContents()
    {
        $directory = $this->directory(['foo/orig0.txt']);
        $dummies   = $this->directory(['foo/bar/.gitkeep', 'baz/.gitkeep']);
        $dummies->file('baz/.gitkeep')->write($contents = 'Baz directory is required');

        $this->command($directory, $dummies)->execute();
        $this->assertFiles(['foo/orig0.txt', 'foo/bar/.gitkeep', 'baz/.gitkeep'], $directory);
        $this->assertMessages(['Creating', 'foo/bar/.gitkeep', 'baz/.gitkeep']);
        $this->assertSame($contents, $directory->file('baz/.gitkeep')->contents());
    }

    public function testRedundantDummyFiles_AreRemoved()
    {
        $directory = $this->directory(['foo/.gitkeep', 'foo/file1.txt', 'bar/.gitkeep', 'bar/file2.txt']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertFiles(['foo/file1.txt', 'bar/file2.txt'], $directory);
        $this->assertMessages(['Removing', 'foo/.gitkeep', 'bar/.gitkeep']);
    }

    public function testHandlingBothMissingAndRedundantDummies()
    {
        $directory = $this->directory(['foo/file1.txt', 'foo/.gitkeep', 'bar/file2.txt']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/baz/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertFiles(['foo/file1.txt', 'bar/file2.txt', 'bar/baz/.gitkeep'], $directory);
        $this->assertMessages(['Creating', 'bar/baz/.gitkeep', 'Removing', 'foo/.gitkeep']);
    }

    private function assertFiles(array $filenames, Files\Directory $directory): void
    {
        $getFilename = fn (Files\File $file) => $file->name();
        $this->assertEquals($filenames, array_map($getFilename, $directory->fileList()));
    }

    private function assertMessages(array $strings = []): void
    {
        $this->assertSame(0, self::$terminal->exitCode());
        $messages = self::$terminal->messagesSent();
        if (!$strings) {
            $this->assertEmpty($messages);
            return;
        }
        foreach ($strings as $index => $string) {
            $this->assertStringContainsString($string, $messages[$index]);
        }
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
        return new Command\HandleDummyFiles($directory, new DummyFiles($files), self::$terminal->reset());
    }
}
