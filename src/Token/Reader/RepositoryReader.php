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

use Shudd3r\PackageFiles\Token\Reader\Data\UserInputData;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Token;


class RepositoryReader extends ValueReader
{
    private File        $gitConfig;
    private ValueReader $fallback;

    protected string $inputPrompt = 'Github repository name';
    protected string $optionName  = 'repo';

    public function __construct(UserInputData $input, File $gitConfig, ValueReader $fallback)
    {
        parent::__construct($input);
        $this->gitConfig = $gitConfig;
        $this->fallback  = $fallback;
    }

    protected function createToken(string $value): Token
    {
        return new Token\Repository($value);
    }

    protected function sourceValue(): string
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
