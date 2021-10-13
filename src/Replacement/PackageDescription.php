<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacement;

use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Reader\FallbackReader;
use Shudd3r\PackageFiles\Application\Token\ValueToken;


class PackageDescription implements Replacement
{
    private string $fallbackToken;

    public function __construct(string $fallbackToken = '')
    {
        $this->fallbackToken = $fallbackToken;
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
        if (!$this->fallbackToken) { return ''; }
        $fallbackValue = $fallback->valueOf($this->fallbackToken);
        return $fallbackValue ? $fallbackValue . ' package' : '';
    }
}
