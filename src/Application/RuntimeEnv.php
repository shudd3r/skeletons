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
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Application\Token\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\Token\Data\MetaData;
use Shudd3r\PackageFiles\Application\Exception;
use Shudd3r\PackageFiles\Environment\Terminal;


class RuntimeEnv
{
    private Directory $package;
    private Directory $skeleton;
    private Terminal  $terminal;
    private Directory $backup;
    private File      $metaFile;

    private ComposerJsonData $composer;
    private MetaData         $metaData;
    private Templates        $templates;

    public function __construct(
        Directory $package,
        Directory $skeleton,
        ?Terminal $terminal = null,
        ?Directory $backup = null,
        ?File $metaFile = null
    ) {
        $this->package  = $this->validDirectory($package);
        $this->skeleton = $this->validDirectory($skeleton);
        $this->terminal = $terminal ?? new Terminal();
        $this->backup   = $backup ?? $this->package->subdirectory('.skeleton-backup');
        $this->metaFile = $metaFile ?? $this->package->file('.github/skeleton.json');
    }

    public function templates(): Templates
    {
        return $this->templates ??= new Templates();
    }

    public function input(): Input
    {
        return $this->terminal;
    }

    public function output(): Output
    {
        return $this->terminal;
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
        return $this->metaFile;
    }

    public function composer(): ComposerJsonData
    {
        return $this->composer ??= new ComposerJsonData($this->package()->file('composer.json'));
    }

    public function metaData(): MetaData
    {
        return $this->metaData ??= new MetaData($this->metaDataFile());
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
