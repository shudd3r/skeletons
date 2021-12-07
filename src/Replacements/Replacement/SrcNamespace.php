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

use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;


class SrcNamespace extends StandardReplacement
{
    protected ?string $inputPrompt  = 'Source files namespace';
    protected ?string $argumentName = 'ns';
    protected string  $description  = <<<'DESC'
        Source files namespace [format: <Vendor>\\<Package>]
        Replaces {%s} placeholder with its value directly
        and {%s.esc} with escaped slashes variant
        DESC;

    private string $fallbackPlaceholder;

    public function __construct(string $fallbackPlaceholder = '')
    {
        $this->fallbackPlaceholder = $fallbackPlaceholder;
    }

    protected function tokenInstance($name, $value): Token
    {
        return Token\CompositeToken::withValueToken(
            new Token\BasicToken($name, $value),
            new Token\BasicToken($name . '.esc', str_replace('\\', '\\\\', $value))
        );
    }

    protected function isValid(string $value): bool
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return false; }
        }

        return true;
    }

    protected function resolvedValue(Source $source): string
    {
        return $this->namespaceFromComposer($source) ?? $this->namespaceFromFallbackValue($source);
    }

    private function namespaceFromComposer(Source $source): ?string
    {
        $composer = $source->composer();
        if (!$psr = $composer->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromFallbackValue(Source $source): string
    {
        $fallbackValue = $this->fallbackPlaceholder ? $source->tokenValueOf($this->fallbackPlaceholder) : '';
        if (!$fallbackValue) { return ''; }

        [$vendor, $package] = explode('/', $fallbackValue) + ['', ''];
        $namespace = $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);

        return $this->isValid($namespace) ? $namespace : '';
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }
}
