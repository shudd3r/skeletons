<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Factory;

use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Files\File;


class InitCommandFactory implements Command\Factory
{
    public function command(RuntimeEnv $env): Command
    {
        $composerFile = new File('composer.json', $env->packageFiles());
        return new Command\GenerateComposer($composerFile);
    }
}
