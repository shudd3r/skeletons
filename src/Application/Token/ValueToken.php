<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token;

use Shudd3r\PackageFiles\Application\Token;


class ValueToken implements Token
{
    private string $placeholder;
    private string $value;

    public function __construct(string $placeholder, string $value)
    {
        $this->placeholder = $placeholder;
        $this->value       = $value;
    }

    public function replacePlaceholders(string $template): string
    {
        return str_replace('{' . $this->placeholder . '}', $this->value, $template);
    }

    public function value(): string
    {
        return $this->value;
    }
}
