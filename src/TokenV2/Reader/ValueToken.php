<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\TokenV2\Reader;

use Shudd3r\PackageFiles\TokenV2\Reader;
use Shudd3r\PackageFiles\TokenV2\Parser;
use Shudd3r\PackageFiles\TokenV2\Source;
use Shudd3r\PackageFiles\Token;


abstract class ValueToken implements Reader, Parser
{
    private Source $source;
    private string $cachedValue;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function token(): ?Token
    {
        $value = $this->value();
        return $this->isValid($value) ? $this->newTokenInstance($value) : null;
    }

    public function value(): string
    {
        return $this->cachedValue ??= $this->source->value($this);
    }

    abstract public function isValid(string $value): bool;

    abstract public function parsedValue(): string;

    abstract protected function newTokenInstance(string $value): Token;
}
