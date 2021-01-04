<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reader;

use Shudd3r\PackageFiles\Application\Token;


class PackageDescription extends ValueReader
{
    public function isValid(string $value): bool
    {
        return !empty($value);
    }

    protected function newTokenInstance(string $value): Token
    {
        return new Token\ValueToken('{description.text}', $value);
    }
}
