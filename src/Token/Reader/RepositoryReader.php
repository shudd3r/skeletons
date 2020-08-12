<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader;

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Token\Reader\Data\UserInputData;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Token;


class RepositoryReader implements Reader
{
    private UserInputData $input;
    private File          $gitConfig;
    private Source        $fallback;

    public function __construct(UserInputData $input, File $gitConfig, Source $fallback)
    {
        $this->input     = $input;
        $this->gitConfig = $gitConfig;
        $this->fallback  = $fallback;
    }

    public function token(): Token
    {
        $value = $this->input->value('Github repository name', 'repo', fn() => $this->readSource());
        return new Token\Repository($value);
    }

    private function readSource()
    {
        return $this->valueFromGitConfig() ?? $this->fallback->value();
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
