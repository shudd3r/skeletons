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
use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Command\Subroutine;


class InitCommandFactory extends Factory
{
    public function command(array $options): Command
    {
        $composerFile     = $this->env->packageFiles()->file('composer.json');
        $generateComposer = new Subroutine\GenerateComposer($composerFile);
        $subroutine       = new Subroutine\ValidateProperties($this->env->output(), $generateComposer);

        return new Command($this->properties($options), $subroutine);
    }
}
