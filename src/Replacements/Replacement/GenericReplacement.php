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
use Shudd3r\Skeletons\Replacements\Token\ValueToken;
use Shudd3r\Skeletons\RuntimeEnv;
use Closure;


class GenericReplacement implements Replacement
{
    private Closure  $default;
    private ?Closure $token;
    private ?Closure $validate;
    private ?string  $optionName;
    private ?string  $inputPrompt;
    private ?string  $description;

    /**
     * @param Closure  $default     fn (RuntimeEnv, FallbackReader) => string
     * @param ?Closure $token       fn (string, string) => ?ValueToken
     * @param ?Closure $validate    fn (string) => bool
     * @param ?string  $inputPrompt
     * @param ?string  $optionName
     * @param ?string  $description
     */
    public function __construct(
        Closure $default,
        ?Closure $token = null,
        ?Closure $validate = null,
        ?string $inputPrompt = null,
        ?string $optionName = null,
        ?string $description = null
    ) {
        $this->default     = $default;
        $this->token       = $token;
        $this->validate    = $validate;
        $this->optionName  = $optionName;
        $this->inputPrompt = $inputPrompt;
        $this->description = $description;
    }

    public function optionName(): ?string
    {
        return $this->optionName;
    }

    public function inputPrompt(): ?string
    {
        return $this->inputPrompt;
    }

    public function description(): string
    {
        return $this->description ?? $this->inputPrompt ?? '';
    }

    public function defaultValue(RuntimeEnv $env, FallbackReader $fallback): string
    {
        return ($this->default)($env, $fallback);
    }

    public function isValid(string $value): bool
    {
        return $this->validate ? ($this->validate)($value) : true;
    }

    public function token(string $name, string $value): ?ValueToken
    {
        return $this->token ? ($this->token)($name, $value) : new ValueToken($name, $value);
    }
}
