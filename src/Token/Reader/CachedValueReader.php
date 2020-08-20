<?php declare(strict_types=1);

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


class CachedValueReader extends ValueReader
{
    private ValueReader $reader;
    private string       $value;

    public function __construct(ValueReader $reader)
    {
        $this->reader = $reader;
    }

    public function createToken(string $value): Token
    {
        return $this->reader->createToken($value);
    }

    public function value(): string
    {
        return $this->value ??= $this->reader->value();
    }

    public function inputPrompt(): string
    {
        return $this->reader->inputPrompt();
    }

    public function optionName(): string
    {
        return $this->reader->optionName();
    }
}
