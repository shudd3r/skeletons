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

    public function testRedundantDummies_SendsFileListWithErrorCode()
    {
        $directory = $this->directory(['foo/.gitkeep', 'foo/orig.txt']);
        $dummies   = $this->directory(['foo/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(1, ['foo/.gitkeep']);
    }

    public function testMissingDummiesInCheckMode_SendsFileListWithErrorCode()
    {
        $directory = $this->directory(['foo/.gitkeep']);
        $dummies   = $this->directory(['foo/.gitkeep', 'bar/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(1, ['bar/.gitkeep']);
    }

    public function testDummyInRootDirectory_IsIgnored()
    {
        $directory = $this->directory();
        $dummies   = $this->directory(['.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(0);
    }

    public function testNonTemplateDummies_AreIgnored()
    {
        $directory = $this->directory(['foo/.gitkeep', 'bar/.gitkeep', 'bar/file.txt']);
        $dummies   = $this->directory(['foo/.gitkeep']);

        $this->command($directory, $dummies)->execute();
        $this->assertOutput(0);
    }

    private function assertOutput(int $exitCode, array $filenames = [])
    {
        $this->assertSame($exitCode, self::$terminal->exitCode());
        if (!$filenames) {
            $this->assertEmpty(self::$terminal->messagesSent());
            return;
        }

        $output = implode('', self::$terminal->messagesSent());
        foreach ($filenames as $filename) {
            $this->assertStringContainsString($filename, $output);
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
