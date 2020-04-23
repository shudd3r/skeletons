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
        $terminal = $this->env->terminal();

        try {
            $this->factory($command)->command($options)->execute();
        } catch (InvalidArgumentException | RuntimeException $e) {
            $terminal->render($e->getMessage(), 1);
        }

        return $terminal->exitCode();
    }

    private function factory(string $command): Command\Factory
    {
        if (!isset($this->factories[$command]) || !class_exists($this->factories[$command])) {
            throw new RuntimeException("Unknown `{$command}` command");
        }

        $className = $this->factories[$command];
        return new $className($this->env);
    }
}
