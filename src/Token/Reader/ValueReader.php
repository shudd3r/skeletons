<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader;

use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Token\Reader;


abstract class ValueReader implements Reader
{
    protected const PROMPT = null;
    protected const OPTION = null;

    public function token(): Token
    {
        return $this->createToken($this->value());
    }

    abstract public function createToken(string $value): Token;

    abstract public function value(): string;

    public function inputPrompt(): string
    {
        return static::PROMPT;
    }

    public function optionName(): string
    {
        return static::OPTION;
    }
}
