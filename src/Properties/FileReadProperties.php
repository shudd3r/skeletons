<?php

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
use Shudd3r\PackageFiles\Files;


class FileReadProperties extends Properties
{
    private Files $packageFiles;
    private array $composerData;

    public function __construct(Files $packageFiles)
    {
        $this->packageFiles = $packageFiles;
    }

    public function repositoryUrl(): string
    {
        if (!$this->packageFiles->exists('.git/config')) { return ''; }

        $config = parse_ini_string($this->packageFiles->contents('.git/config'), true);
        return $config['remote upstream']['url'] ?? $config['remote origin']['url'] ?? '';
    }

    public function packageName(): string
    {
        return $this->composerValue('name') ?? $this->packageNameFromDirectory();
    }

    public function packageDescription(): string
    {
        return $this->composerValue('description') ?? 'Polymorphine library package';
    }

    public function sourceNamespace(): string
    {
        return $this->composerSrcNamespace() ?? '';
    }

    private function packageNameFromDirectory(): string
    {
        $directory = $this->packageFiles->directory();
        return basename(dirname($directory)) . '/' . basename($directory);
    }

    private function composerSrcNamespace(): ?string
    {
        $autoload = $this->composerValue('autoload');
        if (!isset($autoload['psr-4'])) { return null; }

        $namespace = array_search('src/', $autoload['psr-4'], true);
        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function composerValue(string $key): ?string
    {
        if (!isset($this->composerData)) {
            $this->composerData = json_decode($this->packageFiles->contents('composer.json'), true);
        }

        return $this->composerData[$key] ?? null;
    }
}
