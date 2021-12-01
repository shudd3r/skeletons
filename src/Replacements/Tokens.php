<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements;

use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\Token;


class Tokens
{
    private Reader       $reader;
    private Replacements $replacements;

    public function __construct(Replacements $replacements, Reader $reader)
    {
        $this->reader       = $reader;
        $this->replacements = $replacements;
    }

    public function compositeToken(): ?Token
    {
        $tokens = $this->reader->tokens($this->replacements);
        return !in_array(null, $tokens) ? new Token\CompositeToken(...array_values($tokens)) : null;
    }

    public function placeholderValues(): array
    {
        $values = [];
        foreach ($this->reader->tokens($this->replacements) as $name => $token) {
            $value = $token ? $token->value() : null;
            if ($token && $value === null) { continue; }
            $values[$name] = $value;
        }

        return $values;
    }
}
