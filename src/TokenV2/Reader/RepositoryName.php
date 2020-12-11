<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\TokenV2\Reader;

use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\TokenV2\Source;
use Shudd3r\PackageFiles\Token;


class RepositoryName extends ValueToken
{
    private File        $gitConfig;
    private PackageName $packageName;

    public function __construct(File $gitConfig, PackageName $packageName, Source $source = null)
    {
        $this->gitConfig   = $gitConfig;
        $this->packageName = $packageName;
        parent::__construct($source);
    }

    public function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $value);
    }

    public function parsedValue(): string
    {
        return $this->repositoryFromGitConfig() ?? $this->packageName->value();
    }

    protected function newTokenInstance(string $repositoryName): Token
    {
        return new Token\ValueToken('{repository.name}', $repositoryName);
    }

    private function repositoryFromGitConfig(): ?string
    {
        if (!$this->gitConfig->exists()) { return null; }

        $config = parse_ini_string($this->gitConfig->contents(), true);
        if (!$url = $this->remoteUrl($config)) { return null; }

        $path = str_replace(':', '/', $url);
        return basename(dirname($path)) . '/' . basename($path, '.git');
    }

    private function remoteUrl(array $config): ?string
    {
        $url = $config['remote upstream']['url'] ?? $config['remote origin']['url'] ?? '';
        if ($url) { return $url; }

        foreach ($config as $section => $definitions) {
            if (strpos($section, 'remote ') !== 0) { continue; }
            return $definitions['url'] ?? null;
        }

        return null;
    }
}
