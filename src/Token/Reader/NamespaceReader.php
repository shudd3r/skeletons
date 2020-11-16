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


class NamespaceReader extends ValueReader
{
    public function create(string $value): Token
    {
        $token = $this->source->create($value);
        if (!$token) {
            throw new Exception("Invalid namespace `{$value}`");
        }

        return $token;
    }
}
