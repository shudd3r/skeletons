<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacement;

use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Application\Token\ValueToken;


class RepositoryName implements Replacement
{
    private string $fallbackToken;

    public function __construct(string $fallbackToken = '')
    {
        $this->fallbackToken = $fallbackToken;
    }

    public function optionName(): ?string
    {
        return 'repo';
    }

    public function inputPrompt(): ?string
    {
        return 'Github repository name';
    }

    public function defaultValue(RuntimeEnv $env, array $options, Replacements $replacements): string
    {
        return $this->repositoryFromGitConfig($env) ?? $this->fallbackValue($replacements, $options);
    }

    public function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $value);
    }

    public function token(string $name, string $value): ?ValueToken
    {
        return $this->isValid($value) ? new ValueToken($name, $value) : null;
    }

    private function repositoryFromGitConfig(RuntimeEnv $env): ?string
    {
        $gitConfig = $env->package()->file('.git/config');
        if (!$gitConfig->exists()) { return null; }

        $config = parse_ini_string($gitConfig->contents(), true);
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

    private function fallbackValue(Replacements $replacements, array $options): string
    {
        return $this->fallbackToken ? $replacements->valueOf($this->fallbackToken, $options) : '';
    }
}
