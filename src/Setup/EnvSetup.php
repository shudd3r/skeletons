<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Setup;

use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Environment\Terminal;
use Shudd3r\Skeletons\Exception;


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

    public function runtimeEnv(Terminal $terminal, array $ignoredTemplates = []): RuntimeEnv
    {
        $this->validateDirectory($this->package, 'package');
        $this->validateDirectory($this->skeleton, 'skeleton');

        $skeleton = new Directory\TemplateDirectory($this->skeleton, $ignoredTemplates);
        $backup   = $this->backup ?? $this->package->subdirectory(self::BACKUP_DIR);
        $metaFile = $this->metaFile ?? $this->package->file(self::META_FILE);

        return new RuntimeEnv($this->package, $skeleton, $terminal, $backup, $metaFile);
    }

    public function setBackupDirectory(Directory $backup): void
    {
        $this->backup = $backup;
    }

    public function setMetaFile(string $filename): void
    {
        $this->metaFile = $this->package->file($filename);
    }

    private function validateDirectory(Directory $directory, string $name): void
    {
        if ($directory->exists()) { return; }

        $message = "Cannot reach provided $name directory `{$directory->path()}`";
        throw new Exception\InvalidDirectoryException($message);
    }
}
