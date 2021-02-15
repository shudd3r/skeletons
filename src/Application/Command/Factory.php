<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Command;

use Shudd3r\PackageFiles\Environment\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Replacement;


abstract class Factory
{
    public const PACKAGE_NAME  = 'package.name';
    public const PACKAGE_DESC  = 'description.text';
    public const SRC_NAMESPACE = 'namespace.src';
    public const REPO_NAME     = 'repository.name';

    protected RuntimeEnv $env;

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
        $this->setReplacements();
    }

    abstract public function command(array $options): Command;

    protected function setReplacements(): void
    {
        $replacements = $this->env->replacements();

        $replacements->addReplacement(
            self::PACKAGE_NAME,
            fn($env) => new Replacement\PackageName($env)
        );

        $packageName = $replacements->replacement(self::PACKAGE_NAME);
        $replacements->addReplacement(
            self::REPO_NAME,
            fn($env) => new Replacement\RepositoryName($env, $packageName)
        );

        $replacements->addReplacement(
            self::PACKAGE_DESC,
            fn($env) => new Replacement\PackageDescription($this->env, $packageName)
        );

        $replacements->addReplacement(
            self::SRC_NAMESPACE,
            fn($env) => new Replacement\SrcNamespace($this->env, $packageName)
        );
    }
}
