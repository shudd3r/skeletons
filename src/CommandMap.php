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

use Shudd3r\PackageFiles\Command\Factory;
use RuntimeException;


class CommandMap
{
    private RuntimeEnv $env;
    private array      $factories;

    /**
     * @param RuntimeEnv $env
     * @param string[]   $factories Associative array of command names mapped
     *                              to Factory class (FQN) names
     */
    public function __construct(RuntimeEnv $env, array $factories)
    {
        $this->env       = $env;
        $this->factories = $factories;
    }

    /**
     * @param string $name
     *
     * @throws RuntimeException
     *
     * @return Command
     */
    public function command(string $name): Command
    {
        if (!isset($this->factories[$name]) || !class_exists($this->factories[$name])) {
            throw new RuntimeException("Unknown `{$name}` command");
        }

        return $this->factory($this->factories[$name])->command();
    }

    public function factory(string $className): Factory
    {
        return new $className($this->env);
    }
}
