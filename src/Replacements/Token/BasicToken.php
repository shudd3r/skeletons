<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Token;

use Shudd3r\Skeletons\Replacements\Token;


class BasicToken implements Token
{
    private string $placeholder;
    private string $value;

    public function __construct(string $placeholder, string $value)
    {
        $this->placeholder = $placeholder;
        $this->value       = $value;
    }

    public function replace(string $template): string
    {
        return str_replace('{' . $this->placeholder . '}', $this->value, $template);
    }

    public function value(): string
    {
        return $this->value;
    }
}
