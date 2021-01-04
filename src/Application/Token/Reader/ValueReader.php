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

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Validator;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token;


abstract class ValueReader implements Reader, Validator
{
    private Source $source;
    private string $cachedValue;

    public function __construct(?Source $source = null)
    {
        $this->source = $source ?? new Source\PredefinedValue('');
    }

    public function withSource(Source $source): self
    {
        $clone = clone $this;
        $clone->source = $source;
        unset($clone->cachedValue);
        return $clone;
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

    abstract protected function newTokenInstance(string $value): Token;
}
