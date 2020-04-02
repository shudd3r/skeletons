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

use RuntimeException;


class CommandMap
{
    private RuntimeEnv $env;
    private array      $commands;

    /**
     * @param RuntimeEnv $env
     * @param callable[] $commands fn(RuntimeEnv) => Command
     */
    public function __construct(RuntimeEnv $env, array $commands)
    {
        $this->env      = $env;
        $this->commands = $commands;
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
        if (!isset($this->commands[$name])) {
            throw new RuntimeException();
        }

        return ($this->commands[$name])($this->env);
    }
}
