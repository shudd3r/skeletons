<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Reader;

use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Replacement;


class InitialReader extends Reader implements FallbackReader
{
    public function readToken(string $name, Replacement $replacement): void
    {
        if (array_key_exists($name, $this->tokens)) { return; }
        $this->tokens[$name] = null;

        $default = $this->commandLineOption($replacement) ?? $replacement->defaultValue($this->env, $this);
        $this->tokens[$name] = $replacement->token($name, $this->inputString($replacement, $default));
    }

    public function valueOf(string $name): string
    {
        $this->readToken($name, $this->replacements->replacement($name));

        $token = $this->tokens[$name] ?? null;
        return $token ? $token->value() : '';
    }
}
