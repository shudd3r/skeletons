<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\ReaderFactory;

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Token;


class RepositoryNameReaderFactory extends ValueReaderFactory
{
    protected ?string $inputPrompt = 'Github repository name';
    protected ?string $optionName  = 'repo';

    private PackageNameReaderFactory $packageName;

    public function __construct(RuntimeEnv $env, array $options, PackageNameReaderFactory $packageName)
    {
        $this->packageName = $packageName;
        parent::__construct($env, $options);
    }

    public function token(string $name, string $value): ?Token\ValueToken
    {
        $isValid = (bool) preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $value);
        return $isValid ? new Token\ValueToken($name, $value) : null;
    }

    protected function defaultSource(): Source
    {
        $packageName = $this->packageName->initialToken('');
        $config      = $this->env->package()->file('.git/config');
        $callback    = fn() => $this->repositoryFromGitConfig($config) ?? $packageName->value();
        return $this->userSource(new Source\CallbackSource($callback));
    }

    protected function newReaderInstance(Source $source): Reader\ValueReader
    {
        return new Reader\ValueReader($this, $source);
    }

    private function repositoryFromGitConfig(File $gitConfig): ?string
    {
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
