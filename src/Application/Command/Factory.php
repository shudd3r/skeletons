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


abstract class Factory
{
    public const PACKAGE_NAME  = 'package.name';
    public const PACKAGE_DESC  = 'description.text';
    public const SRC_NAMESPACE = 'namespace.src';
    public const REPO_NAME     = 'repository.name';

    protected RuntimeEnv $env;
    protected array      $options;

    public function __construct(RuntimeEnv $env, array $options)
    {
        $this->env     = $env;
        $this->options = $options;
    }

    abstract public function command(): Command;
}
