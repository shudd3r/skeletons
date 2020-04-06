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


class Properties
{
    private string $repoUrl;
    private string $repoName;
    private string $package;
    private string $description;
    private string $namespace;

    public function __construct(string $repo, string $package, string $desc, string $namespace)
    {
        $this->repoUrl     = $repo;
        $this->repoName    = basename(dirname($this->repoUrl)) . '/' . basename($this->repoUrl, '.git');
        $this->package     = $package;
        $this->description = $desc;
        $this->namespace   = $namespace;
    }

    public function repositoryUrl(): string
    {
        return $this->repoUrl;
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
