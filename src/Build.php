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
    private $skeletonFiles;
    private $packageFiles;

    /**
     * @param Terminal $terminal
     * @param Files    $skeletonFiles
     * @param Files    $packageFiles
     */
    public function __construct(Terminal $terminal, Files $skeletonFiles, Files $packageFiles)
    {
        $this->terminal      = $terminal;
        $this->skeletonFiles = $skeletonFiles;
        $this->packageFiles  = $packageFiles;
    }

    /**
     * Builds package environment files.
     *
     * $options array is option name keys ('package', 'repo' & 'desc') with
     * corresponding values. Not provided option values might be omitted or
     * assigned to null.
     *
     * @example Array with all values defined for this package: [
     *     'package' => 'polymorphine/dev',
     *     'repo'    => 'polymorphine/dev',
     *     'desc'    => 'Development tools & coding standard scripts for Polymorphine libraries'
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
        $composer = json_decode($this->packageFiles->contents('composer.json'), true);

        $repo        = $options['repo'] ?? '';
        $package     = $options['package'] ?? $composer['name'] ?? '';
        $description = $options['desc'] ?? $composer['description'] ?? '';

        if (empty($options['package'])) {
            $package = $this->input('Packagist package name', $package ?: $this->packageNameFromDirectory());
        }

        if (empty($options['desc'])) {
            $description = $this->input('Package description', $description ?: 'Polymorphine library package');
        }

        if (empty($options['repo'])) {
            $repo = $this->input('Github repository URL', 'https://github.com/' . $package . '.git');
        }

        return new Properties($this->validGithubUri($repo), $this->validPackagistPackage($package), $description);
    }

    private function packageNameFromDirectory(): string
    {
        $directory = $this->packageFiles->directory();
        return basename(dirname($directory)) . '/' . basename($directory);
    }

    private function validGithubUri(string $uri): string
    {
        $validSuffix = substr($uri, -4) === '.git';
        $validPrefix = substr($uri, 0, 19) === 'https://github.com/' || substr($uri, 0, 15) === 'git@github.com:';

        if (!$validPrefix || !$validSuffix) {
            throw new InvalidArgumentException();
        }

        $repoName = $uri[0] === 'h' ? substr($uri, 19, -4) : substr($uri, 15, -4);
        if (!preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9\-._]{1,100}$#i', $repoName)) {
            throw new InvalidArgumentException();
        }

        return $uri;
    }

    private function validPackagistPackage(string $package): string
    {
        if (!preg_match('#^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9]([_.-]?[a-z0-9]+)*$#iD', $package)) {
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
