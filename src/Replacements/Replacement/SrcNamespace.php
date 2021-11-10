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
use Shudd3r\Skeletons\Replacements\Reader\FallbackReader;
use Shudd3r\Skeletons\Replacements\Token\CompositeValueToken;
use Shudd3r\Skeletons\Replacements\Token\ValueToken;
use Shudd3r\Skeletons\RuntimeEnv;


class SrcNamespace implements Replacement
{
    private string $fallbackName;

    public function __construct(string $fallbackName = '')
    {
        $this->fallbackName = $fallbackName;
    }

    public function optionName(): ?string
    {
        return 'ns';
    }

    public function inputPrompt(): ?string
    {
        return 'Source files namespace';
    }

    public function description(): string
    {
        return $this->inputPrompt() . ' [format: <vendor>\\<namespace>]';
    }

    public function defaultValue(RuntimeEnv $env, FallbackReader $fallback): string
    {
        return $this->namespaceFromComposer($env) ?? $this->namespaceFromFallbackValue($fallback);
    }

    public function isValid(string $value): bool
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return false; }
        }

        return true;
    }

    public function token(string $name, string $value): ?ValueToken
    {
        if (!$this->isValid($value)) { return null; }

        $subToken = new ValueToken($name . '.esc', str_replace('\\', '\\\\', $value));
        return new CompositeValueToken($name, $value, $subToken);
    }

    private function namespaceFromComposer(RuntimeEnv $env): ?string
    {
        if (!$psr = $env->composer()->array('autoload.psr-4')) { return null; }
        $namespace = array_search('src/', $psr, true);

        return $namespace ? rtrim($namespace, '\\') : null;
    }

    private function namespaceFromFallbackValue(FallbackReader $fallback): string
    {
        $fallbackValue = $this->fallbackName ? $fallback->valueOf($this->fallbackName) : '';
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
