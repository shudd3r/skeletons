<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Application\Token\ValueToken;


class FakeReplacement implements Replacement
{
    private ?string $default;
    private ?string $option;
    private ?string $prompt;
    private ?string $fallback;

    public function __construct(
        ?string $default  = null,
        ?string $option   = 'option',
        ?string $prompt   = 'Provide value',
        ?string $fallback = null
    ) {
        $this->default  = $default;
        $this->option   = $option;
        $this->prompt   = $prompt;
        $this->fallback = $fallback;
    }

    public function optionName(): ?string
    {
        return $this->option;
    }

    public function inputPrompt(): ?string
    {
        return $this->prompt;
    }

    public function defaultValue(RuntimeEnv $env, array $options, Replacements $replacements): string
    {
        return $this->default ?? $this->fallbackValue($options, $replacements);
    }

    public function isValid(string $value): bool
    {
        return isset($this->default) || isset($this->fallback);
    }

    public function token(string $name, string $value): ?ValueToken
    {
        return $this->isValid($value) ? new ValueToken($name, $value) : null;
    }

    private function fallbackValue(array $options, Replacements $replacements): string
    {
        return $this->fallback ? $replacements->valueOf($this->fallback, $options) : '';
    }
}
