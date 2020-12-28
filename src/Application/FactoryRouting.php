<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application;

use Shudd3r\PackageFiles\Environment\Routing;
use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\Command\Factory;
use RuntimeException;


class FactoryRouting implements Routing
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

    public function command(string $command, array $options): Command
    {
        if (!isset($this->factories[$command]) || !class_exists($this->factories[$command])) {
            throw new RuntimeException("Unknown `{$command}` command");
        }

        $className = $this->factories[$command];
        return $this->factory($className, $options)->command();
    }

    private function factory(string $className, $options): Factory
    {
        return new $className($this->runtimeEnv, $options);
    }
}
