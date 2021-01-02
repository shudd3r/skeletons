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
use RuntimeException;


class ValueListToken implements Token
{
    private string $placeholder;
    private array  $values;

    public function __construct(string $placeholder, string ...$values)
    {
        $this->placeholder = $placeholder;
        $this->values      = $values;
    }

    public function replacePlaceholders(string $template): string
    {
        $templateParts = explode($this->placeholder, $template);
        if (count($templateParts) !== count($this->values) + 1) {
            throw new RuntimeException();
        }

        $generated = array_shift($templateParts);
        foreach ($this->values as $value) {
            $generated .= $value . array_shift($templateParts);
        }

        return $generated;
    }
}