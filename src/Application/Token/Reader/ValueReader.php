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
use Shudd3r\PackageFiles\Application\Token\TokenFactory;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token;


class ValueReader implements Reader
{
    private TokenFactory $factory;
    private Source       $source;
    private string       $cachedValue;

    public function __construct(TokenFactory $factory, ?Source $source = null)
    {
        $this->factory = $factory;
        $this->source  = $source ?? new Source\PredefinedValue('');
    }

    public function withSource(Source $source): self
    {
        $clone = clone $this;
        $clone->source = $source;
        unset($clone->cachedValue);
        return $clone;
    }

    public function token(string $namespace = ''): ?Token
    {
        return $this->factory->token($namespace, $this->value());
    }

    public function value(): string
    {
        return $this->cachedValue ??= $this->source->value($this);
    }
}
