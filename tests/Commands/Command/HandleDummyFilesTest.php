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

    public function testNoRedundantDummiesInCheckMode_SendNoOutput()
    {
        $directory = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);
        $dummies   = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);

        $this->command($directory, $dummies, false)->execute();

        $this->assertEmpty(self::$terminal->messagesSent());
        $this->assertSame(0, self::$terminal->exitCode());
    }

    public function testRedundantDummiesInCheckMode_SendFileListAndErrorMessage()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt', 'foo/orig.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt']);

        $this->command($directory, $dummies, false)->execute();

        $messageStream = implode('', self::$terminal->messagesSent());
        $this->assertStringContainsString('foo/dummy0.txt', $messageStream);
        $this->assertStringContainsString('foo/dummy1.txt', $messageStream);
        $this->assertNotSame(0, self::$terminal->exitCode());
    }

    public function testDummiesInRootDirectory_AreNotRemovedAndNoMessagesAreSent()
    {
        $directory = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);
        $dummies   = $this->directory(['dummy0.txt', 'dummy1.txt', 'dummy2.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertCount(3, $dummies->fileList());
        $this->assertEmpty(self::$terminal->messagesSent());
    }

    public function testDummiesWithOriginalFileInSameDirectory_AreRemovedAndFileListIsSent()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt', 'foo/orig.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt']);

        $this->command($directory, $dummies)->execute();

        $messageStream = implode('', self::$terminal->messagesSent());
        $this->assertStringContainsString('foo/dummy0.txt', $messageStream);
        $this->assertStringContainsString('foo/dummy1.txt', $messageStream);
        $this->assertSame(0, self::$terminal->exitCode());
        $this->assertEmpty($dummies->fileList());
    }

    public function testDummiesWithoutOriginalFilesInSameDirectory_AreNotRemoved()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt', 'foo/dummy2.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/dummy1.txt', 'foo/dummy2.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertCount(3, $dummies->fileList());
    }

    public function testDummiesWithOriginalFilesInSubdirectories_AreRemoved()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/bar/orig.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertEmpty($dummies->fileList());
    }

    public function testDummiesWithSingleSiblingsInSubdirectory_AreRemoved()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/bar/dummy1.txt', 'other/orig.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/bar/dummy1.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertCount(1, $dummies->fileList());
        $this->assertFalse($dummies->file('foo/dummy0.txt')->exists());
        $this->assertTrue($dummies->file('foo/bar/dummy1.txt')->exists());
    }

    public function testDummiesWithOriginalFilesInSameSubdirectory_AreRemoved()
    {
        $directory = $this->directory(['foo/dummy0.txt', 'foo/bar/dummy1.txt', 'foo/bar/baz.txt']);
        $dummies   = $this->directory(['foo/dummy0.txt', 'foo/bar/dummy1.txt']);

        $this->command($directory, $dummies)->execute();

        $this->assertEmpty($dummies->fileList());
    }

    public function testRedundantDummiesInMultipleDirectories_AreRemoved()
    {
        $fileStructure = [
            'orig0' => 'foo/orig0.txt', 'foo/dummy0.txt',
            'foo/bar/dummy1.txt',
            'bar/dummy2.txt', 'orig1' => 'bar/orig1.txt',
            'orig2' => 'bar/baz/orig2,txt'
        ];
        $directory = $this->directory($fileStructure);

        unset($fileStructure['orig0'], $fileStructure['orig1'], $fileStructure['orig2']);
        $fileStructure[] = 'bar/baz/removed-dummy.txt';
        $dummies = $this->directory($fileStructure);

        $this->command($directory, $dummies)->execute();

        $expectedDummies = $this->directory(['foo/bar/dummy1.txt', 'bar/baz/removed-dummy.txt']);
        $this->assertEquals($expectedDummies->fileList(), $dummies->fileList());
    }

    private function directory(array $subdirectories = []): Doubles\FakeDirectory
    {
        $directory = new Doubles\FakeDirectory();
        foreach ($subdirectories as $file) {
            $directory->addFile($file);
        }

        return $directory;
    }

    private function command(Files\Directory $directory, Files $files, bool $remove = true): Command
    {
        return new Command\HandleDummyFiles($directory, $files, self::$terminal->reset(), $remove);
    }
}
