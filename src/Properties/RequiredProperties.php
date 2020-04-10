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
use Shudd3r\PackageFiles\Terminal;
use InvalidArgumentException;


class RequiredProperties extends Properties
{
    private Properties $properties;
    private Terminal   $terminal;

    private string $repoUrl;
    private string $package;
    private string $description;
    private string $namespace;

    public function __construct(Properties $properties, Terminal $terminal, array $options)
    {
        $this->properties = $properties;
        $this->terminal   = $terminal;
        $this->setProperties($options);
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

    private function setProperties(array $options): void
    {
        $interactive = isset($options['i']) || isset($options['interactive']);

        $package = $options['package'] ?? $this->properties->packageName();
        if ($interactive && empty($options['package'])) {
            $package = $this->input('Packagist package name', $package);
        }

        $description = $options['desc'] ?? $this->properties->packageDescription() ?: 'Polymorphine library package';
        if ($interactive && empty($options['desc'])) {
            $description = $this->input('Package description', $description);
        }

        $repo = $options['repo'] ?? $this->properties->repositoryUrl() ?: 'https://github.com/' . $package . '.git';
        if ($interactive && empty($options['repo'])) {
            $repo = $this->input('Github repository URL', $repo);
        }

        $namespace = $options['ns'] ?? $this->properties->sourceNamespace() ?? $this->namespaceFromPackage($package);
        if ($interactive && empty($options['ns'])) {
            $namespace = $this->input('Source files namespace', $namespace);
        }

        $repo      = $this->validGithubUri($repo);
        $package   = $this->validPackagistPackage($package);
        $namespace = $this->validNamespace($namespace);

        $this->repoUrl     = $repo;
        $this->package     = $package;
        $this->description = $description;
        $this->namespace   = $namespace;
    }

    private function namespaceFromPackage(string $package)
    {
        [$vendor, $package] = explode('/', $package);
        return $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
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

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->display($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
