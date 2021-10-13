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

use Shudd3r\PackageFiles\Application\Token\Reader\FallbackReader;


class FakeFallbackReader implements FallbackReader
{
    private array $tokenValues;

    public function __construct(array $tokenValues = [])
    {
        $this->tokenValues = $tokenValues;
    }

    public function valueOf(string $name): string
    {
        return $this->tokenValues[$name] ?? '';
    }
}
