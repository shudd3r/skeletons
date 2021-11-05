<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Templates\Factory;


class Templates
{
    private RuntimeEnv $env;
    private array      $factories;

    private Files $files;

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

    public function generatedFiles(array $ignore = []): Files
    {
        $this->files = new Files\Directory\TemplateDirectory($this->env->skeleton(), $ignore);
        return new Files\ReflectedFiles($this->env->package(), $this->files);
    }

    private function factory(string $filename): ?Factory
    {
        return $this->factories[$filename] ?? null;
    }

    private function basicTemplate(string $filename): Template
    {
        $this->files ?? $this->generatedFiles();
        $contents = $this->files->file($filename)->contents();
        return new Template\BasicTemplate($contents);
    }
}
