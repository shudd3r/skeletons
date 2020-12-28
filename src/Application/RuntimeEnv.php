<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application;

use Shudd3r\PackageFiles\Environment\Input;
use Shudd3r\PackageFiles\Environment\Output;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Exception;


class RuntimeEnv
{
    private Input     $input;
    private Output    $output;
    private Directory $package;
    private Directory $skeleton;
    private Directory $backup;
    private File      $metaData;

    public function __construct(
        Input $input,
        Output $output,
        Directory $package,
        Directory $skeleton,
        ?Directory $backup = null,
        ?File $metaDataFile = null
    ) {
        $this->input    = $input;
        $this->output   = $output;
        $this->package  = $this->validDirectory($package);
        $this->skeleton = $this->validDirectory($skeleton);
        $this->backup   = $backup ?? $this->package->subdirectory('.skeleton-backup');
        $this->metaData = $metaDataFile ?? $this->package->file('.github/skeleton.json');
    }

    public function input(): Input
    {
        return $this->input;
    }

    public function output(): Output
    {
        return $this->output;
    }

    public function package(): Directory
    {
        return $this->package;
    }

    public function skeleton(): Directory
    {
        return $this->skeleton;
    }

    public function backup(): Directory
    {
        return $this->backup;
    }

    public function metaDataFile(): File
    {
        return $this->metaData;
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
