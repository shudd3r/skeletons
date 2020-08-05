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


class PrioritySearch implements Source
{
    /** @var Source[] */
    private array $sources;

    public function __construct(Source ...$sources)
    {
        $this->sources = $sources;
    }

    public function value(): string
    {
        $value = '';
        foreach ($this->sources as $source) {
            if ($value = $source->value()) { return $value; }
        }

        return $value;
    }
}
