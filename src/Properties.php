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
    public string $repoUrl;
    public string $repoName;
    public string $package;
    public string $description;
    public string $namespace;

    public function __construct(string $repo, string $package, string $desc, string $namespace)
    {
        $this->repoUrl     = $repo;
        $this->repoName    = basename(dirname($this->repoUrl)) . '/' . basename($this->repoUrl, '.git');
        $this->package     = $package;
        $this->description = $desc;
        $this->namespace   = $namespace;
    }
}
