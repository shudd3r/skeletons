<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Rework\Setup\Builder;

use Shudd3r\Skeletons\Rework\Replacements\Replacement;
use Shudd3r\Skeletons\Rework\Replacements\Source;
use Closure;


class ReplacementBuilder
{
    private Closure  $resolvedValue;
    private ?Closure $isValid       = null;
    private ?Closure $tokenInstance = null;
    private ?string  $inputPrompt   = null;
    private ?string  $argumentName  = null;
    private string   $description   = '';

    /**
     * @param Closure  $resolvedValue fn (Source) => string
     *
     * @see Source
     */
    public function __construct(Closure $resolvedValue)
    {
        $this->resolvedValue = $resolvedValue;
    }

    /**
     * @param Closure $isValid fn (string $value) => bool
     */
    public function validate(Closure $isValid): self
    {
        $this->isValid = $isValid;
        return $this;
    }

    /**
     * @param Closure $tokenInstance fn (string $placeholder, string $value) => Token
     */
    public function token(Closure $tokenInstance): self
    {
        $this->tokenInstance = $tokenInstance;
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
            $this->resolvedValue,
            $this->isValid,
            $this->tokenInstance,
            $this->inputPrompt,
            $this->argumentName,
            $this->description
        );
    }
}
