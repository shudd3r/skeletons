<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Replacements\Replacement;

use Shudd3r\PackageFiles\Application\Replacements\Replacement;
use Shudd3r\PackageFiles\Application\Replacements\Reader\FallbackReader;
use Shudd3r\PackageFiles\Application\Replacements\Token\ValueToken;
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class PackageDescription implements Replacement
{
    private string $fallbackName;

    public function __construct(string $fallbackName = '')
    {
        $this->fallbackName = $fallbackName;
    }

    public function optionName(): ?string
    {
        return 'desc';
    }

    public function inputPrompt(): ?string
    {
        return 'Package description';
    }

    public function token(string $name, string $value): ?ValueToken
    {
        return $this->isValid($value) ? new ValueToken($name, $value) : null;
    }

    public function isValid(string $value): bool
    {
        return !empty($value);
    }

    public function defaultValue(RuntimeEnv $env, FallbackReader $fallback): string
    {
        return $env->composer()->value('description') ?? $this->descriptionFromFallbackValue($fallback);
    }

    private function descriptionFromFallbackValue(FallbackReader $fallback): string
    {
        if (!$this->fallbackName) { return ''; }
        $fallbackValue = $fallback->valueOf($this->fallbackName);
        return $fallbackValue ? $fallbackValue . ' package' : '';
    }
}
