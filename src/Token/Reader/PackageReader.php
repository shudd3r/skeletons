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


class PackageReader extends ValueReader
{
    protected function createToken(string $value): Token
    {
        if (!preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value)) {
            throw new Exception("Invalid packagist package name `{$value}`");
        }

        return new Token\CompositeToken(
            new Token\ValueToken('{package.name}', $value),
            new Token\ValueToken('{package.title}', $this->titleName($value))
        );
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
