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
use Shudd3r\Skeletons\Templates\TemplateFiles;


class Templates
{
    private RuntimeEnv    $env;
    private TemplateFiles $files;
    private array         $factories;

    public function __construct(RuntimeEnv $env, TemplateFiles $files, array $factories)
    {
        $this->env       = $env;
        $this->files     = $files;
        $this->factories = $factories;
    }

    public function template(string $filename): Template
    {
        $factory = $this->factory($filename);
        $file    = $this->files->file($filename);
        return $factory ? $factory->template($file, $this->env) : new Template\BasicTemplate($file->contents());
    }

    public function generatedFiles(InputArgs $args): Files
    {
        $types = $args->command() === 'init' ? ['orig', 'init', 'dummy'] : ['orig'];
        if ($args->includeLocalFiles()) { $types[] = 'local'; }

        return new Files\ReflectedFiles($this->env->package(), $this->files->files($types));
    }

    public function dummyFiles(): Files
    {
        return new Files\ReflectedFiles($this->env->package(), $this->files->files(['dummy']));
    }

    private function factory(string $filename): ?Factory
    {
        return $this->factories[$filename] ?? null;
    }
}
