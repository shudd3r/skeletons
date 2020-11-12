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
use Exception;


class RepositoryReader extends ValueReader
{
    protected function createToken(string $value): Token
    {
        if (!preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $value)) {
            throw new Exception("Invalid github repository name `{$value}`");
        }

        return new Token\ValueToken('{repository.name}', $value);
    }
}
