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


class PackageProperties implements Properties
{
    private string $repoName;
    private string $package;
    private string $description;
    private string $namespace;

    public function __construct(string $repoName, string $package, string $description, string $namespace) {
        $this->repoName    = $repoName;
        $this->package     = $package;
        $this->description = $description;
        $this->namespace   = $namespace;
    }

    public function repositoryName(): string
    {
        return $this->repoName;
    }

    public function packageName(): string
    {
        return $this->package;
    }

    public function packageDescription(): string
    {
        return $this->description;
    }

    public function sourceNamespace(): string
    {
        return $this->namespace;
    }
}
