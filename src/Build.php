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

use Shudd3r\PackageFiles\Command\GenerateComposer;
use Shudd3r\PackageFiles\Files\File;
use InvalidArgumentException;


class Build
{
    private $terminal;
    private $packageFiles;

    public function __construct(RuntimeEnv $env)
    {
        $this->terminal     = $env->terminal();
        $this->packageFiles = $env->packageFiles();
    }

    /**
     * Builds package environment files.
     *
     * $options array is config name keys ('package', 'repo', 'desc' & 'ns')
     * with corresponding values and 'interactive' key (or short version: 'i')
     * with any not null value (also false) that when given activates CLI input
     * of not provided options (omitted or assigned to null) otherwise these
     * values will try to be resolved automatically.
     *
     * @example Array with all values defined for this package: [
     *     'package' => 'polymorphine/dev',
     *     'repo'    => 'polymorphine/dev',
     *     'desc'    => 'Development tools & coding standard scripts for Polymorphine libraries',
     *     'ns'      => 'Polymorphine\Dev'
     * ]
     *
     * @param array $options
     */
    public function run(array $options = []): void
    {
        try {
            $packageProperties = $this->packageProperties($options);
        } catch (InvalidArgumentException $e) {
            $this->terminal->display($e->getMessage());
            return;
        }

        $composerFile = new File('composer.json', $this->packageFiles);
        $command      = new GenerateComposer($composerFile);

        $command->execute($packageProperties);
    }

    private function packageProperties(array $options): Properties
    {
        $composer    = json_decode($this->packageFiles->contents('composer.json'), true);
        $interactive = isset($options['i']) || isset($options['interactive']);

        $package = $options['package'] ?? $composer['name'] ?? $this->packageNameFromDirectory();
        if ($interactive && empty($options['package'])) {
            $package = $this->input('Packagist package name', $package);
        }

        $description = $options['desc'] ?? $composer['description'] ?? 'Polymorphine library package';
        if ($interactive && empty($options['desc'])) {
            $description = $this->input('Package description', $description);
        }

        $repo = $options['repo'] ?? $this->readGitConfig() ?? 'https://github.com/' . $package . '.git';
        if ($interactive && empty($options['repo'])) {
            $repo = $this->input('Github repository URL', $repo);
        }

        $namespace = $options['ns'] ?? $this->composerSrcNamespace($composer) ?? $this->namespaceFromPackage($package);
        if ($interactive && empty($options['ns'])) {
            $namespace = $this->input('Source files namespace', $namespace);
        }

        $repo    = $this->validGithubUri($repo);
        $package = $this->validPackagistPackage($package);

        return new Properties($repo, $package, $description, $namespace);
    }

    private function packageNameFromDirectory(): string
    {
        $directory = $this->packageFiles->directory();
        return basename(dirname($directory)) . '/' . basename($directory);
    }

    private function composerSrcNamespace(array $composer): ?string
    {
        if (!isset($composer['autoload']['psr-4'])) { return null; }
        return array_search('src/', $composer['autoload']['psr-4'], true) ?: null;
    }

    private function namespaceFromPackage(string $package)
    {
        [$vendor, $package] = explode('/', $package);
        return $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);
    }

    private function toPascalCase(string $name): string
    {
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }

    private function readGitConfig(): ?string
    {
        if (!$this->packageFiles->exists('.git/config')) { return null; }

        $config = parse_ini_string($this->packageFiles->contents('.git/config'), true);
        return $config['remote upstream']['url'] ?? $config['remote origin']['url'] ?? null;
    }

    private function validGithubUri(string $uri): string
    {
        $validSuffix = substr($uri, -4) === '.git';
        $validPrefix = substr($uri, 0, 19) === 'https://github.com/' || substr($uri, 0, 15) === 'git@github.com:';

        if (!$validPrefix || !$validSuffix) {
            throw new InvalidArgumentException();
        }

        $repoName = $uri[0] === 'h' ? substr($uri, 19, -4) : substr($uri, 15, -4);
        if (!preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $repoName)) {
            throw new InvalidArgumentException();
        }

        return $uri;
    }

    private function validPackagistPackage(string $package): string
    {
        if (!preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $package)) {
            throw new InvalidArgumentException();
        }

        return $package;
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->display($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
