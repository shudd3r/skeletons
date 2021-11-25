<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Replacements\Token;


class FakeToken implements Token
{
    private ?string $value;
    private string  $name;

    public function __construct(string $name, ?string $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    public function replace(string $template): string
    {
        return str_replace('{' . $this->name . '}', $this->value ?? 'null-value', $template);
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
