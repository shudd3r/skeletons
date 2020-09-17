<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Token\Reader\PackageReader;


class DefaultRepository implements Source
{
    private File          $gitConfig;
    private PackageReader $package;

    public function __construct(File $gitConfig, PackageReader $package)
    {
        $this->gitConfig = $gitConfig;
        $this->package   = $package;
    }

    public function value(): string
    {
        return $this->valueFromGitConfig() ?? $this->package->value();
    }

    private function valueFromGitConfig(): ?string
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