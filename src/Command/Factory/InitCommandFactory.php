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
use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Files\File;


class InitCommandFactory extends Factory
{
    public function command(): Command
    {
        $packageFiles = $this->env->packageFiles();
        $terminal     = $this->env->terminal();

        $composerFile     = new File('composer.json', $this->env->packageFiles());
        $generateComposer = new Subroutine\GenerateComposer($composerFile);
        $subroutine       = new Subroutine\ValidateProperties($terminal, $generateComposer);

        $buildProperties = function (array $options) use ($packageFiles, $terminal): Properties {
            $properties = new Properties\FileReadProperties($packageFiles);
            $properties = new Properties\PredefinedProperties($options, $properties);
            $properties = new Properties\ResolvedProperties($properties, $packageFiles);
            if (isset($options['i']) || isset($options['interactive'])) {
                $properties = new Properties\InputProperties($terminal, $properties);
            }
            return new Properties\CachedProperties($properties);
        };

        return new Command\InitCommand($buildProperties, $subroutine, $terminal);
    }
}
