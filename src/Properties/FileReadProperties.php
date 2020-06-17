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
use RuntimeException;


class FileReadProperties implements Properties
{
    private Directory $packageFiles;
    private array     $composerData;

    public function __construct(Directory $packageFiles)
    {
        $this->packageFiles = $packageFiles;
    }

    public function repositoryName(): string
    {
        $gitConfigFile = $this->packageFiles->file('.git/config');
        if (!$gitConfigFile->exists()) { return ''; }

        $config = parse_ini_string($gitConfigFile->contents(), true);
        $uri    = $config['remote upstream']['url'] ?? $config['remote origin']['url'] ?? '';
        if (!$uri) { return ''; }

        $uriPath = str_replace(':', '/', $uri);
        return basename(dirname($uriPath)) . '/' . basename($uriPath, '.git');
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
            $this->composerData = $this->generateComposerData();
        }

        return $this->composerData[$key] ?? null;
    }

    private function generateComposerData()
    {
        $composerJsonFile = $this->packageFiles->file('composer.json');
        $composerData     = $composerJsonFile->exists() ? json_decode($composerJsonFile->contents(), true) : [];

        if (!is_array($composerData)) {
            throw new RuntimeException('Invalid composer.json file');
        }

        return $composerData;
    }
}
