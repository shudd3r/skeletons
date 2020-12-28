<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Command;

use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class BackupFiles implements Command
{
    private Directory $package;
    private Directory $backup;

    public function __construct(Directory $package, Directory $backup)
    {
        $this->package = $package;
        $this->backup  = $backup;
    }

    public function execute(): void
    {
        foreach ($this->package->files() as $packageFile) {
            if (!$packageFile->exists()) { continue; }
            $this->copyToBackupDirectory($packageFile);
        }
    }

    private function copyToBackupDirectory(File $packageFile): void
    {
        $backupFile = $this->backup->file($packageFile->name());
        $backupFile->write($packageFile->contents());
    }
}
