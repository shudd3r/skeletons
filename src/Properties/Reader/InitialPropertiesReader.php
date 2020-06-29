<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Properties\Reader;

use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Properties;


class InitialPropertiesReader implements Properties\Reader
{
    private RuntimeEnv $env;

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
    }

    public function properties(array $options): Properties
    {
        $properties = new Properties\FileReadProperties($this->env->packageFiles());
        $properties = new Properties\PredefinedProperties($options, $properties);
        $properties = new Properties\ResolvedProperties($properties, $this->env->packageFiles());
        if (isset($options['i']) || isset($options['interactive'])) {
            $properties = new Properties\InputProperties($this->env->input(), $properties);
        }
        return new Properties\PackageProperties(
            $properties->repositoryName(),
            $properties->packageName(),
            $properties->packageDescription(),
            $properties->sourceNamespace()
        );
    }
}
