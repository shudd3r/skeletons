<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Environment\Input;
use Shudd3r\Skeletons\Environment\Output;
use Shudd3r\Skeletons\Environment\Terminal;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Replacements\Data\ComposerJsonData;
use Shudd3r\Skeletons\Replacements\Data\MetaData;


class RuntimeEnv
{
    private Directory $package;
    private Directory $skeleton;
    private Terminal  $terminal;
    private Directory $backup;
    private File      $metaFile;

    private ComposerJsonData $composer;
    private MetaData         $metaData;

    public function __construct(Directory $package, Directory $skeleton, Terminal $terminal, Directory $backup, File $metaFile)
    {
        $this->package  = $package;
        $this->skeleton = $skeleton;
        $this->terminal = $terminal;
        $this->backup   = $backup;
        $this->metaFile = $metaFile;
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
}
