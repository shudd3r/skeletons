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
use Shudd3r\Skeletons\Replacements\Reader\FallbackReader;
use Shudd3r\Skeletons\RuntimeEnv;
use Closure;


class ReplacementBuilder
{
    private Closure  $default;
    private ?Closure $token       = null;
    private ?Closure $validate    = null;
    private ?string  $optionName  = null;
    private ?string  $inputPrompt = null;
    private ?string  $description = null;

    /**
     * @see RuntimeEnv
     * @see FallbackReader
     *
     * @param Closure  $default fn (RuntimeEnv, FallbackReader) => string
     */
    public function __construct(Closure $default)
    {
        $this->default = $default;
    }

    /**
     * @param Closure $token fn (string $placeholder, string $value) => ?ValueToken
     */
    public function token(Closure $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param Closure $validate fn (string $value) => bool
     */
    public function validate(Closure $validate): self
    {
        $this->validate = $validate;
        return $this;
    }

    public function inputPrompt(string $inputPrompt): self
    {
        $this->inputPrompt = $inputPrompt;
        return $this;
    }

    public function optionName(string $optionName): self
    {
        $this->optionName = $optionName;
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
            $this->default,
            $this->token,
            $this->validate,
            $this->inputPrompt,
            $this->optionName,
            $this->description
        );
    }
}
