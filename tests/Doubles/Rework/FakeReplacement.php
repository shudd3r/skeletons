<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles\Rework;

use Shudd3r\Skeletons\Rework\Replacements\Replacement;
use Shudd3r\Skeletons\Rework\Replacements\Source;
use Shudd3r\Skeletons\Replacements\Token;


class FakeReplacement implements Replacement
{
    private string $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function token(string $name, Source $source): ?Token
    {
        if (strpos($this->value, 'get-') === 0) { $this->value = $source->tokenValueOf(substr($this->value, 4)); }
        return new Token\BasicToken($name, $this->value);
    }

    public function description(): string
    {
        return $this->value;
    }
}
