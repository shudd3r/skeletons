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

use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\Files\File;


class InitCommandFactory extends Factory
{
    public function command(): Subroutine
    {
        $composerFile     = new File('composer.json', $this->env->packageFiles());
        $generateComposer = new Subroutine\GenerateComposer($composerFile);

        return new Subroutine\ValidateProperties($this->env->terminal(), $generateComposer);
    }
}
