<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Replacement;

use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Source;


class RepositoryName extends Replacement
{
    protected ?string $inputPrompt  = 'Remote git repository name';
    protected ?string $argumentName = 'repo';
    protected string  $description  = <<<'DESC'
        Remote git repository name [format: <owner>/<repository>]
        Replaces {%s} placeholder
        DESC;

    private string $fallbackPlaceholder;

    public function __construct(string $fallbackPlaceholder = '')
    {
        $this->fallbackPlaceholder = $fallbackPlaceholder;
    }

    protected function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $value);
    }

    protected function resolvedValue(Source $source): string
    {
        return $this->repositoryFromGitConfig($source->fileContents('.git/config')) ?? $this->fallbackValue($source);
    }

    private function repositoryFromGitConfig(string $gitConfig): ?string
    {
        if (!$gitConfig) { return null; }

        $config = parse_ini_string($gitConfig, true);
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

    private function fallbackValue(Source $source): string
    {
        return $this->fallbackPlaceholder ? $source->tokenValueOf($this->fallbackPlaceholder) : '';
    }
}
