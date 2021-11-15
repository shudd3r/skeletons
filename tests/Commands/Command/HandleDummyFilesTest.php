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

    public function testNoInvalidDummiesInCheckMode_SendNoOutput()
    {
        $directory = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);
        $dummies   = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);

        $this->command($directory, $dummies, true)->execute();

        $this->assertEmpty(self::$terminal->messagesSent());
        $this->assertSame(0, self::$terminal->exitCode());
    }

    public function testRedundantDummiesInCheckMode_SendFileListAndErrorMessage()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt', 'foo/orig.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt']);

        $this->command($directory, $dummies, true)->execute();

        $messageStream = implode('', self::$terminal->messagesSent());
        $this->assertStringContainsString('foo/dummy1.txt', $messageStream);
        $this->assertNotSame(0, self::$terminal->exitCode());
    }

    public function testMissingDummiesInCheckMode_SendFileListAndErrorMessage()
    {
        $directory = $this->directory(['foo/dummy0.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'bar/dummy1.txt']);

        $this->command($directory, $dummies, true)->execute();

        $messageStream = implode('', self::$terminal->messagesSent());
        $this->assertNotSame(0, self::$terminal->exitCode());
        $this->assertStringContainsString('bar/dummy1.txt', $messageStream);
        $this->assertFiles($directory, ['foo/dummy0.txt']);
    }

    public function testDummiesInRootDirectory_AreIgnored()
    {
        $directory = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);
        $dummies   = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertCount(3, $directory->fileList());
        $this->assertEmpty(self::$terminal->messagesSent());
    }

    public function testDummiesWithOriginalFileInSameDirectory_AreRemovedAndFileListIsSent()
    {
        $directory = $this->directory(['foo/dummy1.txt', 'foo/dummy2.txt', 'foo/orig.txt']);
        $dummies   = $this->directory(['foo/dummy1.txt', 'foo/dummy2.txt']);

        $this->command($directory, $dummies)->execute();

        $messageStream = implode('', self::$terminal->messagesSent());
        $this->assertStringContainsString('foo/dummy1.txt', $messageStream);
        $this->assertStringContainsString('foo/dummy2.txt', $messageStream);
        $this->assertSame(0, self::$terminal->exitCode());
        $this->assertFiles($directory, ['foo/orig.txt']);
    }

    public function testDummiesWithOriginalFilesInSubdirectories_AreRemoved()
    {
        $directory = $this->directory(['foo/dummy.txt', 'foo/bar/orig.txt']);
        $dummies   = $this->directory(['foo/dummy.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertFiles($directory, ['foo/bar/orig.txt']);
    }

    public function testMultipleDummiesInSameDirectoryWithoutOriginalFiles_AreNotRemoved()
    {
        $directory = $this->directory(['foo/dummy1.txt', 'foo/dummy2.txt']);
        $dummies   = $this->directory(['foo/dummy1.txt', 'foo/dummy2.txt', 'foo/dummy3.txt']);

        $this->command($directory, $dummies)->execute();
        $this->assertCount(2, $directory->fileList());
    }

    public function testDummiesWithSiblingsInSubdirectory_AreRemoved()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/bar/dummy1.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/bar/dummy1.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertFiles($directory, ['foo/bar/dummy1.txt']);
    }

    public function testDummiesWithOriginalFilesInSameSubdirectory_AreRemoved()
    {
        $directory = $this->directory(['foo/baz/dummy0.txt', 'foo/bar/dummy1.txt', 'foo/bar/baz.txt']);
        $dummies   = $this->directory(['foo/baz/dummy0.txt', 'foo/bar/dummy1.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertFiles($directory, ['foo/baz/dummy0.txt', 'foo/bar/baz.txt']);
    }

    public function testRedundantDummiesInMultipleDirectories_AreRemoved()
    {
        $files = [
            'orig'    => ['foo/orig0.txt', 'bar/orig1.txt', 'bar/baz/orig2,txt'],
            'dummies' => ['foo/dummy0.txt', 'bar/dummy1.txt', 'foo/bar/dummy3.txt']
        ];
        $directory = $this->directory(array_merge($files['orig'], $files['dummies']));
        $dummies   = $this->directory(array_merge($files['dummies'], ['bar/baz/removed-dummy.txt']));

        $this->command($directory, $dummies)->execute();

        $this->assertFiles($directory, array_merge($files['orig'], ['foo/bar/dummy3.txt']));
    }

    public function testMissingDummyFiles_AreCreated()
    {
        $directory = $this->directory($files = ['foo/orig0.txt', 'bar/orig1.txt', 'bar/baz/orig2,txt']);
        $dummies   = $this->directory(['foo/bar/dummy3.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertFiles($directory, array_merge($files, ['foo/bar/dummy3.txt']));
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

    private function directory(array $subdirectories = []): Doubles\FakeDirectory
    {
        $directory = new Doubles\FakeDirectory();
        foreach ($subdirectories as $file) {
            $directory->addFile($file);
        }

        return $directory;
    }

    private function command(Files\Directory $directory, Files $files, bool $validate = false): Command
    {
        return new Command\HandleDummyFiles($directory, $files, self::$terminal->reset(), $validate);
    }
}
