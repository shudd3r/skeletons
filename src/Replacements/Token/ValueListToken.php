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

    public function replace(string $template): string
    {
        $templateParts = explode('{' . $this->placeholder . '}', $template);
        if (count($templateParts) !== count($this->values) + 1) {
            throw new RuntimeException('Cannot match replacements list to placeholders');
        }

        $generated = array_shift($templateParts);
        foreach ($this->values as $value) {
            $generated .= $value . array_shift($templateParts);
        }

        return $generated;
    }
}
