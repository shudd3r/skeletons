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

use Shudd3r\Skeletons\Replacements\StandardReplacement;
use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;
use Closure;


class GenericReplacement extends StandardReplacement
{
    private Closure  $resolveValue;
    private ?Closure $validate;
    private ?Closure $createToken;

    /**
     * @param Closure  $resolveValue fn (Source) => string
     * @param ?Closure $validate     fn (string) => bool
     * @param ?Closure $createToken  fn (string, string) => Token
     * @param ?string  $inputPrompt
     * @param ?string  $argumentName
     * @param ?string  $description
     */
    public function __construct(
        Closure $resolveValue,
        ?Closure $validate = null,
        ?Closure $createToken = null,
        ?string $inputPrompt = null,
        ?string $argumentName = null,
        ?string $description = null
    ) {
        $this->resolveValue = $resolveValue;
        $this->validate     = $validate;
        $this->createToken  = $createToken;
        $this->argumentName = $argumentName;
        $this->inputPrompt  = $inputPrompt;
        $this->description  = $description ?? '';
    }

    protected function tokenInstance($name, $value): Token
    {
        return $this->createToken ? ($this->createToken)($name, $value) : parent::tokenInstance($name, $value);
    }

    protected function isValid(string $value): bool
    {
        return $this->validate ? ($this->validate)($value) : true;
    }

    protected function resolvedValue(Source $source): string
    {
        return ($this->resolveValue)($source);
    }
}
