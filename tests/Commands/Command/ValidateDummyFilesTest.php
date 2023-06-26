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


class ValidateDummyFilesTest extends TestCase
{
    private static Doubles\MockedTerminal $terminal;

    public static function setUpBeforeClass(): void
    {
        self::$terminal = new Doubles\MockedTerminal();
    }

    public function testNoInvalidDummies_SendsNoOutput()
    {
        $directory = $this->directory(['foo/.gitkeep', 'file1.txt', 'bar/file2.txt']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(0);
    }

    public function testMissingDummiesInCheckMode_SendsFileListWithErrorCode()
    {
        $directory = $this->directory(['foo/.gitkeep']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep', '.baz/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(1, ['Missing', 'bar/.gitkeep', '.baz/.gitkeep', 'create']);
    }

    public function testRedundantDummies_SendsFileListWithErrorCode()
    {
        $directory = $this->directory(['foo/.gitkeep', 'foo/file.txt', 'bar/.gitkeep', 'bar/baz/file.txt']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(1, ['Redundant', 'foo/.gitkeep', 'bar/.gitkeep', 'remove']);
    }

    public function testListingBothMissingAndRedundantDummyFiles()
    {
        $directory = $this->directory(['root.txt', 'foo/file.txt', 'foo/.gitkeep']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(1, ['Missing', 'bar/.gitkeep', 'create', 'Redundant', 'foo/.gitkeep', 'remove']);
    }

    private function assertOutput(int $exitCode, array $strings = [])
    {
        $this->assertSame($exitCode, self::$terminal->exitCode());
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
        return new Command\ValidateDummyFiles($directory, new DummyFiles($files), self::$terminal->reset());
    }
}
