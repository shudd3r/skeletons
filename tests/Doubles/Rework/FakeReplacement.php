<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles\Rework;

use Shudd3r\Skeletons\Rework\Replacements\Replacement;
use Shudd3r\Skeletons\Rework\Replacements\Source;


class FakeReplacement extends Replacement
{
    private string $value;
    private bool   $isFallback;

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

    public function withPrompt(string $inputPrompt): self
    {
        $clone = clone $this;
        $clone->inputPrompt = $inputPrompt;
        return $clone;
    }

    public function withInputArg(string $argumentName): self
    {
        $clone = clone $this;
        $clone->argumentName = $argumentName;
        return $clone;
    }

    protected function isValid(string $value): bool
    {
        return $value !== 'invalid';
    }

    protected function resolvedValue(Source $source): string
    {
        return $this->isFallback ? $source->tokenValueOf($this->value) : $this->value;
    }
}
