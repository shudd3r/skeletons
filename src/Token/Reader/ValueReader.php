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

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Token\Reader\Data\UserInputData;
use Shudd3r\PackageFiles\Token;


abstract class ValueReader implements Reader
{
    protected UserInputData $input;

    protected string $inputPrompt;
    protected string $optionName;

    private string $value;

    public function __construct(UserInputData $input)
    {
        $this->input = $input;
    }

    public function token(): Token
    {
        return $this->createToken($this->value());
    }

    public function value(): string
    {
        return $this->value ??= $this->readValue();
    }

    abstract protected function createToken(string $value): Token;

    abstract protected function sourceValue(): string;

    private function readValue(): string
    {
        $default = isset($this->optionName)
            ? $this->input->commandLineOption($this->optionName) ?? $this->sourceValue()
            : $this->sourceValue();

        return isset($this->inputPrompt)
            ? $this->input->value($this->inputPrompt, $default)
            : $default;
    }
}
