<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Setup\Builder;

use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Source;
use Closure;


class ReplacementBuilder
{
    private Closure  $resolveValue;
    private ?Closure $validate     = null;
    private ?Closure $createToken  = null;
    private ?string  $inputPrompt  = null;
    private ?string  $argumentName = null;
    private string   $description  = '';

    /**
     * @param Closure $resolveValue fn (Source) => string
     *
     * @see Source
     */
    public function __construct(Closure $resolveValue)
    {
        $this->resolveValue = $resolveValue;
    }

    /**
     * @param Closure $validate fn (string $value) => bool
     */
    public function validate(Closure $validate): self
    {
        $this->validate = $validate;
        return $this;
    }

    /**
     * @param Closure $createToken fn (string $placeholder, string $value) => Token
     */
    public function token(Closure $createToken): self
    {
        $this->createToken = $createToken;
        return $this;
    }

    public function inputPrompt(string $inputPrompt): self
    {
        $this->inputPrompt = $inputPrompt;
        return $this;
    }

    public function argumentName(string $argumentName): self
    {
        $this->argumentName = $argumentName;
        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function build(): Replacement
    {
        return new Replacement\GenericReplacement(
            $this->resolveValue,
            $this->validate,
            $this->createToken,
            $this->inputPrompt,
            $this->argumentName,
            $this->description
        );
    }
}
