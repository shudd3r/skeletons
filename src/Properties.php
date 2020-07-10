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

use Shudd3r\PackageFiles\Properties\Repository;
use Shudd3r\PackageFiles\Properties\Package;
use Shudd3r\PackageFiles\Properties\MainNamespace;


class Properties
{
    private Repository    $repository;
    private Package       $package;
    private MainNamespace $namespace;

    public function __construct(Repository $repository, Package $package, MainNamespace $namespace)
    {
        $this->repository = $repository;
        $this->package    = $package;
        $this->namespace  = $namespace;
    }

    public function repositoryName(): string
    {
        return $this->repository->name();
    }

    public function packageName(): string
    {
        return $this->package->name();
    }

    public function packageDescription(): string
    {
        return $this->package->description();
    }

    public function sourceNamespace(): string
    {
        return $this->namespace->src();
    }
}
