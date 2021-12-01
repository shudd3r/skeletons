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
use Shudd3r\Skeletons\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;
use Closure;


class GenericReplacement extends Replacement
{
    private Closure  $resolvedValue;
    private ?Closure $isValid;
    private ?Closure $tokenInstance;

    /**
     * @param Closure  $resolvedValue fn (Source) => string
     * @param ?Closure $isValid       fn (string) => bool
     * @param ?Closure $tokenInstance fn (string, string) => Token
     * @param ?string  $inputPrompt
     * @param ?string  $argumentName
     * @param ?string  $description
     */
    public function __construct(
        Closure $resolvedValue,
        ?Closure $isValid = null,
        ?Closure $tokenInstance = null,
        ?string $inputPrompt = null,
        ?string $argumentName = null,
        ?string $description = null
    ) {
        $this->resolvedValue = $resolvedValue;
        $this->isValid       = $isValid;
        $this->tokenInstance = $tokenInstance;
        $this->argumentName  = $argumentName;
        $this->inputPrompt   = $inputPrompt;
        $this->description   = $description ?? '';
    }

    protected function tokenInstance($name, $value): Token
    {
        return $this->tokenInstance ? ($this->tokenInstance)($name, $value) : parent::tokenInstance($name, $value);
    }

    protected function isValid(string $value): bool
    {
        return $this->isValid ? ($this->isValid)($value) : true;
    }

    protected function resolvedValue(Source $source): string
    {
        return ($this->resolvedValue)($source);
    }
}
