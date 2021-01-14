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


class PackageName extends ValueReader
{
    protected function newTokenInstance(string $namespace, string $packageName): Token
    {
        return new Token\CompositeToken(
            new Token\ValueToken($namespace, $packageName),
            new Token\ValueToken($namespace . '.title', $this->titleName($packageName))
        );
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
