<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command;

use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\File;


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
        $packageFiles = array_filter($this->package->files(), fn(File $file) => $file->exists());

        foreach ($packageFiles as $packageFile) {
            $this->backup->file($packageFile->name())->write($packageFile->contents());
        }
    }
}
