<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Source\Decorator;

use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\Token;


class CachedValue implements Source
{
    private Source $source;
    private ?string $value = null;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function create(string $value): ?Token
    {
        return $this->source->create($value);
    }

    public function value(): string
    {
        return $this->value ??= $this->source->value();
    }
}
