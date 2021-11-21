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


class InitialReader extends Reader implements FallbackReader
{
    public function readToken(string $name): bool
    {
        $this->tokens[$name] = null;

        $replacement = $this->replacements->replacement($name);
        $default     = $this->commandLineOption($replacement) ?? $replacement->defaultValue($this->env, $this);
        $token       = $replacement->token($name, $this->inputString($replacement, $default));

        $this->tokens[$name] = $token;
        return $token !== null;
    }

    public function valueOf(string $name): string
    {
        if (!array_key_exists($name, $this->tokens)) {
            $this->readToken($name);
        }

        $token = $this->tokens[$name] ?? null;
        return $token ? $token->value() : '';
    }
}
