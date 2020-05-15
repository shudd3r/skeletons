<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command;

use Shudd3r\PackageFiles\RuntimeEnv;
use RuntimeException;


class Routing
{
    private RuntimeEnv $runtimeEnv;
    private array      $factories;

    /**
     * @param RuntimeEnv $runtimeEnv
     * @param string[]   $factories
     */
    public function __construct(RuntimeEnv $runtimeEnv, array $factories)
    {
        $this->runtimeEnv = $runtimeEnv;
        $this->factories  = $factories;
    }

    public function factory(string $command): Factory
    {
        if (!isset($this->factories[$command]) || !class_exists($this->factories[$command])) {
            throw new RuntimeException("Unknown `{$command}` command");
        }

        $className = $this->factories[$command];
        return new $className($this->runtimeEnv);
    }
}
