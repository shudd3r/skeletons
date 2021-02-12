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

    private array $tokenReaders;

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
    }

    abstract public function command(array $options): Command;

    protected function replacements(): array
    {
        if (isset($this->tokenReaders)) { return $this->tokenReaders; }

        $packageName = new Replacement\PackageName($this->env);

        return $this->tokenReaders = [
            self::PACKAGE_NAME  => $packageName,
            self::REPO_NAME     => new Replacement\RepositoryName($this->env, $packageName),
            self::PACKAGE_DESC  => new Replacement\PackageDescription($this->env, $packageName),
            self::SRC_NAMESPACE => new Replacement\SrcNamespace($this->env, $packageName)
        ];
    }
}
