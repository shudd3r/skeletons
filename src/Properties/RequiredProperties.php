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
use InvalidArgumentException;


class RequiredProperties extends Properties
{
    private string $repoUrl;
    private string $package;
    private string $description;
    private string $namespace;

    public function __construct(Properties $properties)
    {
        $this->repoUrl     = $this->validGithubUri($properties->repositoryUrl());
        $this->package     = $this->validPackagistPackage($properties->packageName());
        $this->description = $properties->packageDescription();
        $this->namespace   = $this->validNamespace($properties->sourceNamespace());
    }

    public function repositoryUrl(): string
    {
        return $this->repoUrl;
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

    private function validGithubUri(string $uri): string
    {
        $validSuffix = substr($uri, -4) === '.git';
        $validPrefix = substr($uri, 0, 19) === 'https://github.com/' || substr($uri, 0, 15) === 'git@github.com:';

        if (!$validPrefix || !$validSuffix) {
            throw new InvalidArgumentException("Invalid github uri `{$uri}`");
        }

        $repoName = $uri[0] === 'h' ? substr($uri, 19, -4) : substr($uri, 15, -4);
        if (!preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $repoName)) {
            throw new InvalidArgumentException("Invalid github uri `{$uri}`");
        }

        return $uri;
    }

    private function validPackagistPackage(string $package): string
    {
        if (!preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $package)) {
            throw new InvalidArgumentException("Invalid packagist package name `{$package}`");
        }

        return $package;
    }

    private function validNamespace(string $namespace): string
    {
        foreach (explode('\\', $namespace) as $label) {
            if (!preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label)) {
                throw new InvalidArgumentException("Invalid namespace `{$namespace}`");
            }
        }
        return $namespace;
    }
}
