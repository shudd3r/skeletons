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

use Shudd3r\PackageFiles\Application\Input;
use Shudd3r\PackageFiles\Token;


class InputReader extends ValueReader
{
    private Input       $input;
    private ValueReader $reader;

    public function __construct(Input $input, ValueReader $reader)
    {
        $this->input  = $input;
        $this->reader = $reader;
    }

    public function createToken(string $value): Token
    {
        return $this->reader->createToken($value);
    }

    public function value(): string
    {
        $default = $this->reader->value();

        return $this->input->value($this->reader->inputPrompt() . ' [default: ' . $default . ']:') ?: $default;
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
