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
    protected function createToken(string $value): Token
    {
        foreach (explode('\\', $value) as $label) {
            if (!preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label)) {
                throw new Exception("Invalid label `{$label}` in `{$value}` namespace");
            }
        }

        return new Token\CompositeToken(
            new Token\ValueToken('{namespace.src}', $value),
            new Token\ValueToken('{namespace.src.esc}', str_replace('\\', '\\\\', $value))
        );
    }
}
