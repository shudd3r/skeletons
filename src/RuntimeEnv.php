<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\Input;
use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Exception;


class RuntimeEnv
{
    private Input     $input;
    private Output    $output;
    private Directory $packageFiles;
    private Directory $skeletonFiles;
    private Directory $backupFiles;

    public function __construct(
        Input $input,
        Output $output,
        Directory $packageFiles,
        Directory $skeletonFiles,
        Directory $backupFiles = null
    ) {
        $this->input         = $input;
        $this->output        = $output;
        $this->packageFiles  = $this->validDirectory($packageFiles);
        $this->skeletonFiles = $this->validDirectory($skeletonFiles);
        $this->backupFiles   = $backupFiles ?? $this->packageFiles->subdirectory('.skeleton-backup');
    }

    public function input(): Input
    {
        return $this->input;
    }

    public function output(): Output
    {
        return $this->output;
    }

    public function packageDirectory(): Directory
    {
        return $this->packageFiles;
    }

    public function skeletonDirectory(): Directory
    {
        return $this->skeletonFiles;
    }

    public function backupDirectory(): Directory
    {
        return $this->backupFiles;
    }

    private function validDirectory(Directory $directory): Directory
    {
        if (!$directory->exists()) {
            $message = "Cannot reach provided directory `{$directory->path()}`";
            throw new Exception\InvalidDirectoryException($message);
        }

        return $directory;
    }
}
