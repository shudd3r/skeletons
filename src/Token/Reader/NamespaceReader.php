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
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token;


class NamespaceReader extends ValueReader
{
    private ComposerJsonData $composer;
    private ValueReader      $fallback;

    protected string $inputPrompt = 'Source files namespace';
    protected string $optionName  = 'ns';

    public function __construct(UserInputData $input, ComposerJsonData $composer, ValueReader $fallback)
    {
        parent::__construct($input);
        $this->composer = $composer;
        $this->fallback = $fallback;
    }

    protected function createToken(string $value): Token
    {
        return new Token\MainNamespace($value);
    }

    protected function sourceValue(): string
    {
        return $this->namespaceFromComposer() ?? $this->namespaceFromPackageName();
    }

    private function namespaceFromComposer(): ?string
    {
        if (!$psr = $this->composer->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromPackageName(): string
    {
        [$vendor, $package] = explode('/', $this->fallback->value());
        return $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }
}
