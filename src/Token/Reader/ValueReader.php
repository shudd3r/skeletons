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

use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Token;


abstract class ValueReader implements Reader, Source
{
    private Source  $source;
    private ?string $value = null;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function token(): Token
    {
        return $this->createToken($this->value());
    }

    public function value(): string
    {
        return $this->value ??= $this->source->value();
    }

    abstract protected function createToken(string $value): Token;
}