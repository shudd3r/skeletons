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

use Shudd3r\PackageFiles\Application\Token\TokenFactory;
use Shudd3r\PackageFiles\Application\Token;


class FakeTokenFactory implements TokenFactory
{
    private bool $isValid;

    public function __construct(bool $validationResult = true)
    {
        $this->isValid = $validationResult;
    }

    public function token(string $name, string $value): ?Token\ValueToken
    {
        return $this->isValid ? new Token\ValueToken($name, $value) : null;
    }
}
