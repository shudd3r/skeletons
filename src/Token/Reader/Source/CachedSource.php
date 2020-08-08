<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Token\Reader\Source;


class CachedSource implements Source
{
    private Source  $source;
    private ?string $value = null;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function value(): string
    {
        return isset($this->value) ? $this->value : $this->value = $this->source->value();
    }
}
