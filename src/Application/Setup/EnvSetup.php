<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Setup;

use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Environment\Terminal;


class EnvSetup
{
    protected const META_FILE  = '.github/skeleton.json';
    protected const BACKUP_DIR = '.skeleton-backup';

    private Directory $package;
    private Directory $skeleton;
    private Directory $backup;
    private File      $metaFile;

    public function __construct(Directory $package, Directory $skeleton)
    {
        $this->package  = $package;
        $this->skeleton = $skeleton;
    }

    public function runtimeEnv(Terminal $terminal): RuntimeEnv
    {
        $backup   = $this->backup ?? $this->package->subdirectory(self::BACKUP_DIR);
        $metaFile = $this->metaFile ?? $this->package->file(self::META_FILE);

        return new RuntimeEnv($this->package, $this->skeleton, $terminal, $backup, $metaFile);
    }

    public function setBackupDirectory(Directory $backup): void
    {
        $this->backup = $backup;
    }

    public function setMetaFile(string $filename): void
    {
        $this->metaFile = $this->package->file($filename);
    }
}
