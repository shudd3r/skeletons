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
use Shudd3r\PackageFiles\Application\RuntimeEnv;


class SrcNamespace extends ValueReader
{
    public function isValid(string $value): bool
    {
        foreach (explode('\\', $value) as $label) {
            $isValidLabel = (bool) preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label);
            if (!$isValidLabel) { return false; }
        }

        return true;
    }

    protected function newTokenInstance(string $namespace, string $value): Token
    {
        return new Token\CompositeToken(
            new Token\ValueToken(RuntimeEnv::SRC_NAMESPACE, $value),
            new Token\ValueToken(RuntimeEnv::SRC_NAMESPACE_ESC, str_replace('\\', '\\\\', $value))
        );
    }
}
