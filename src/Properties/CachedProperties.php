<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Properties;

use Shudd3r\PackageFiles\Properties;


class CachedProperties extends Properties
{
    private Properties $properties;

    private string $repoUrl;
    private string $repoName;
    private string $package;
    private string $description;
    private string $namespace;

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function repositoryUrl(): string
    {
        return $this->repoUrl ??= $this->properties->repositoryUrl();
    }

    public function repositoryName(): string
    {
        return $this->repoName ??= parent::repositoryName();
    }

    public function packageName(): string
    {
        return $this->package ??= $this->properties->packageName();
    }

    public function packageDescription(): string
    {
        return $this->description ??= $this->properties->packageDescription();
    }

    public function sourceNamespace(): string
    {
        return $this->namespace ??= $this->properties->sourceNamespace();
    }
}
