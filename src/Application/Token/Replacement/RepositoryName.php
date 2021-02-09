<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Replacement;

use Shudd3r\PackageFiles\Application\Token\Replacement;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class RepositoryName extends Replacement
{
    protected ?string $inputPrompt = 'Github repository name';
    protected ?string $optionName  = 'repo';

    private PackageName $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageName $packageName)
    {
        $this->packageName = $packageName;
        parent::__construct($env, $options);
    }

    protected function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $value);
    }

    protected function defaultSource(): Source
    {
        $callback = fn() => $this->repositoryFromGitConfig() ?? $this->packageName->sourceValue();
        return $this->userSource(new Source\CallbackSource($callback));
    }

    private function repositoryFromGitConfig(): ?string
    {
        $gitConfig = $this->env->package()->file('.git/config');
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
}
