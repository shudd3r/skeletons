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


class CommandOptionReader extends ValueReader
{
    private string      $option;
    private ValueReader $reader;

    public function __construct(string $option, ValueReader $reader)
    {
        $this->option = $option;
        $this->reader = $reader;
    }

    public function createToken(string $value): Token
    {
        return $this->reader->createToken($value);
    }

    public function value(): string
    {
        return $this->option;
    }
}
