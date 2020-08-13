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
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token;


class NamespaceReader implements Reader
{
    private UserInputData    $input;
    private ComposerJsonData $composer;
    private Source           $fallback;

    public function __construct(UserInputData $input, ComposerJsonData $composer, Source $fallback)
    {
        $this->input    = $input;
        $this->composer = $composer;
        $this->fallback = $fallback;
    }

    public function token(): Token
    {
        $value = $this->input->value('Source files namespace', 'ns', fn() => $this->readSource());
        return new Token\MainNamespace($value);
    }

    private function readSource(): string
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
