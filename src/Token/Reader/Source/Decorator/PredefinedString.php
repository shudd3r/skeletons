<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source\Decorator;

use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Token;


class PredefinedString implements Source
{
    private string $value;
    private Source $source;

    public function __construct(string $value, Source $source)
    {
        $this->value  = $value;
        $this->source = $source;
    }

    public function create(string $value): ?Token
    {
        return $this->source->create($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
