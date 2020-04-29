<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\Terminal;
use Shudd3r\PackageFiles\Application\Input;
use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class RuntimeEnv
{
    private Terminal  $terminal;
    private Directory $packageFiles;
    private Directory $skeletonFiles;

    public function __construct(Terminal $terminal, Directory $packageFiles, Directory $skeletonFiles)
    {
        $this->terminal      = $terminal;
        $this->packageFiles  = $packageFiles;
        $this->skeletonFiles = $skeletonFiles;
    }

    public function output(): Output
    {
        return $this->terminal;
    }

    public function input(): Input
    {
        return $this->terminal;
    }

    public function packageFiles(): Directory
    {
        return $this->packageFiles;
    }

    public function skeletonFiles(): Directory
    {
        return $this->skeletonFiles;
    }
}
