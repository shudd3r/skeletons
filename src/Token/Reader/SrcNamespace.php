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

use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\Token;


class SrcNamespace extends ValueToken
{
    private ComposerJsonData $composer;
    private PackageName      $packageName;

    public function __construct(ComposerJsonData $composer, PackageName $packageName, Source $source = null)
    {
        $this->composer    = $composer;
        $this->packageName = $packageName;
        parent::__construct($source);
    }

    public function isValid(string $value): bool
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return false; }
        }

        return true;
    }

    public function parsedValue(): string
    {
        return $this->namespaceFromComposer() ?? $this->namespaceFromPackageName();
    }

    protected function newTokenInstance(string $value): Token
    {
        return new Token\CompositeToken(
            new Token\ValueToken('{namespace.src}', $value),
            new Token\ValueToken('{namespace.src.esc}', str_replace('\\', '\\\\', $value))
        );
    }

    private function namespaceFromComposer(): ?string
    {
        if (!$psr = $this->composer->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromPackageName(): string
    {
        [$vendor, $package] = explode('/', $this->packageName->value());
        return $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }
}
