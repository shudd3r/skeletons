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
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class FileReadProperties extends Properties
{
    private Directory $packageFiles;
    private array     $composerData;

    public function __construct(Directory $packageFiles)
    {
        $this->packageFiles = $packageFiles;
    }

    public function repositoryUrl(): string
    {
        $gitConfigFile = $this->packageFiles->file('.git/config');
        if (!$gitConfigFile->exists()) { return ''; }

        $config = parse_ini_string($gitConfigFile->contents(), true);
        return $config['remote upstream']['url'] ?? $config['remote origin']['url'] ?? '';
    }

    public function packageName(): string
    {
        return $this->composerValue('name') ?? '';
    }

    public function packageDescription(): string
    {
        return $this->composerValue('description') ?? '';
    }

    public function sourceNamespace(): string
    {
        return $this->composerSrcNamespace() ?? '';
    }

    private function composerSrcNamespace(): ?string
    {
        $autoload = $this->composerValue('autoload');
        if (!isset($autoload['psr-4'])) { return null; }

        $namespace = array_search('src/', $autoload['psr-4'], true);
        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function composerValue(string $key)
    {
        if (!isset($this->composerData)) {
            $composerJsonFile = $this->packageFiles->file('composer.json');
            $this->composerData = $composerJsonFile->exists() ? json_decode($composerJsonFile->contents(), true) : [];
        }

        return $this->composerData[$key] ?? null;
    }
}
