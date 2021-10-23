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

use Shudd3r\PackageFiles\Templates\Template;
use Shudd3r\PackageFiles\Templates\Factory;


class Templates
{
    private RuntimeEnv $env;
    private array      $factories;

    public function __construct(RuntimeEnv $env, array $factories = [])
    {
        $this->env       = $env;
        $this->factories = $factories;
    }

    public function template(string $filename): Template
    {
        $factory = $this->factory($filename);
        return $factory ? $factory->template($filename, $this->env) : $this->basicTemplate($filename);
    }

    private function factory(string $filename): ?Factory
    {
        return $this->factories[$filename] ?? null;
    }

    private function basicTemplate(string $filename): Template
    {
        $contents = $this->env->skeleton()->file($filename)->contents();
        return new Template\BasicTemplate($contents);
    }
}
