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


abstract class Properties
{
    private $repositoryName;

    abstract public function repositoryUrl(): string;

    public function repositoryName(): string
    {
        return $this->repositoryName ??= $this->resolveFromUrl();
    }

    abstract public function packageName(): string;

    abstract public function packageDescription(): string;

    abstract public function sourceNamespace(): string;

    private function resolveFromUrl(): string
    {
        $url = str_replace(':', '/', $this->repositoryUrl());
        return $url ? basename(dirname($url)) . '/' . basename($url, '.git') : '';
    }
}
