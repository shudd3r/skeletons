<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Source;

use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token;


class CodeNamespace implements Source
{
    private ComposerJsonData $composer;
    private Source           $fallback;

    public function __construct(ComposerJsonData $composer, Source $fallback)
    {
        $this->composer = $composer;
        $this->fallback = $fallback;
    }

    public function token(string $value): ?Token
    {
        foreach (explode('\\', $value) as $label) {
            $validLabel = preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$validLabel) { return null; }
        }

        return new Token\CompositeToken(
            new Token\ValueToken('{namespace.src}', $value),
            new Token\ValueToken('{namespace.src.esc}', str_replace('\\', '\\\\', $value))
        );
    }

    public function value(): string
    {
        return $this->namespaceFromComposer() ?? $this->namespaceFromFallbackSource();
    }

    private function namespaceFromComposer(): ?string
    {
        if (!$psr = $this->composer->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromFallbackSource(): string
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
