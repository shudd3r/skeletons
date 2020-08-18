<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Token\Reader\Data\UserInputData;
use Shudd3r\PackageFiles\Token\Reader\ValueReader;
use Shudd3r\PackageFiles\Token;


class FakeValueReader extends ValueReader
{
    private string $value;

    public function __construct(UserInputData $input, string $value = '')
    {
        parent::__construct($input);
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    protected function createToken(string $value): Token
    {
        return new FakeToken($value);
    }

    protected function sourceValue(): string
    {
        return $this->value;
    }
}
