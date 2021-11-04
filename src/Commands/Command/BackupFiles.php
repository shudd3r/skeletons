<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Command;

use Shudd3r\Skeletons\Commands\Command;
use Shudd3r\Skeletons\Environment\FileSystem\Directory;
use Shudd3r\Skeletons\Environment\FileSystem\File;


class BackupFiles implements Command
{
    private Directory $expected;
    private Directory $backup;

    public function __construct(Directory $expected, Directory $backup)
    {
        $this->expected = $expected;
        $this->backup   = $backup;
    }

    public function execute(): void
    {
        foreach ($this->expected->fileList() as $expectedFile) {
            if (!$expectedFile->exists()) { continue; }
            $this->copyToBackupDirectory($expectedFile);
        }
    }

    private function copyToBackupDirectory(File $packageFile): void
    {
        $this->backup->file($packageFile->name())->write($packageFile->contents());
    }
}
