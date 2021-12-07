<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Closure;
use Shudd3r\Skeletons\Replacements\Replacement\StandardReplacement;
use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;


class FakeReplacement extends StandardReplacement
{
    private string   $value;
    private bool     $isFallback;
    private ?Closure $validate = null;

    public function __construct(string $value = '', bool $isFallback = false)
    {
        $this->value      = $value;
        $this->isFallback = $isFallback;
    }

    public static function create(string $value = '', bool $isFallback = false): self
    {
        return new self($value, $isFallback);
    }

    public function withDescription(string $description): self
    {
        $clone = clone $this;
        $clone->description = $description;
        return $clone;
    }

    public function withPrompt(string $inputPrompt, int $tries = 3): self
    {
        $clone = clone $this;
        $clone->inputPrompt = $inputPrompt;
        $clone->inputTries  = $tries;
        return $clone;
    }

    public function withInputArg(string $argumentName): self
    {
        $clone = clone $this;
        $clone->argumentName = $argumentName;
        return $clone;
    }

    public function withValidation(Closure $validate): self
    {
        $clone = clone $this;
        $clone->validate = $validate;
        return $clone;
    }

    protected function tokenInstance($name, $value): Token
    {
        $token = parent::tokenInstance($name, $value);
        return $value === 'null' ? new Token\CompositeToken($token) : $token;
    }

    protected function isValid(string $value): bool
    {
        return $this->validate ? ($this->validate)($value) : $value !== 'invalid';
    }

    protected function resolvedValue(Source $source): string
    {
        return $this->isFallback ? $source->tokenValueOf($this->value) : $this->value;
    }
}
