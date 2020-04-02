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


class RuntimeEnv
{
    private Terminal $terminal;
    private Files    $packageFiles;
    private Files    $skeletonFiles;

    public function __construct(Terminal $terminal, Files $packageFiles, Files $skeletonFiles)
    {
        $this->terminal      = $terminal;
        $this->packageFiles  = $packageFiles;
        $this->skeletonFiles = $skeletonFiles;
    }

    public function terminal(): Terminal
    {
        return $this->terminal;
    }

    public function packageFiles(): Files
    {
        return $this->packageFiles;
    }

    public function skeletonFiles(): Files
    {
        return $this->skeletonFiles;
    }
}
