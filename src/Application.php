<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use InvalidArgumentException;
use RuntimeException;


class Application
{
    private RuntimeEnv $env;
    private array      $factories;

    public function __construct(RuntimeEnv $env, array $factories)
    {
        $this->env       = $env;
        $this->factories = $factories;
    }

    /**
     * Builds package environment files.
     *
     * $options array is config name keys ('package', 'repo', 'desc' & 'ns')
     * with corresponding values and 'interactive' key (or short version: 'i')
     * with any not null value (also false) that when given activates CLI input
     * of not provided options (omitted or assigned to null) otherwise these
     * values will try to be resolved automatically.
     *
     * @example Array with all values defined for this package: [
     *     'package' => 'polymorphine/dev',
     *     'repo'    => 'polymorphine/dev',
     *     'desc'    => 'Development tools & coding standard scripts for Polymorphine libraries',
     *     'ns'      => 'Polymorphine\Dev'
     * ]
     *
     * @param string $command
     * @param array  $options
     *
     * @return int Exit code
     */
    public function run(string $command, array $options = []): int
    {
        try {
            return $this->command($command)->execute($options);
        } catch (InvalidArgumentException | RuntimeException $e) {
            $this->env->terminal()->display($e->getMessage());
        }

        return 1;
    }

    private function command(string $name): Command
    {
        if (!isset($this->factories[$name]) || !class_exists($this->factories[$name])) {
            throw new RuntimeException("Unknown `{$name}` command");
        }

        return $this->factory($this->factories[$name])->command();
    }

    private function factory(string $className): Command\Factory
    {
        return new $className($this->env);
    }
}
