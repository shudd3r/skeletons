<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Template;

use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Exception;


class Templates
{
    private RuntimeEnv $env;
    private array      $factories;

    public function __construct(RuntimeEnv $env, array $factories = [])
    {
        $this->env       = $env;
        $this->factories = $factories;
    }

    public function add(string $filename, Factory $factory): void
    {
        if (isset($this->factories[$filename])) {
            throw new Exception\TemplateOverwriteException();
        }

        $this->factories[$filename] = $factory;
    }

    public function template(File $skeletonFile): Template
    {
        $factory = $this->factory($skeletonFile->name());
        return $factory
            ? $factory->template($skeletonFile, $this->env)
            : new Template\BasicTemplate($skeletonFile->contents());
    }

    private function factory(string $filename): ?Factory
    {
        return $this->factories[$filename] ?? null;
    }
}
