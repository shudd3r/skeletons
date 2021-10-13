<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reader;

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Replacement;


class InitialReader extends Reader implements FallbackReader
{
    public function readToken(string $name, Replacement $replacement): void
    {
        if (array_key_exists($name, $this->tokens)) { return; }
        $this->tokens[$name] = null;

        $default  = $this->commandLineOption($replacement) ?? $replacement->defaultValue($this->env, $this);
        $initial  = $this->inputString($replacement, $replacement->isValid($default) ? $default : '');

        $this->tokens[$name] = $replacement->token($name, $initial);
    }

    public function valueOf(string $name): string
    {
        $this->readToken($name, $this->replacements->replacement($name));

        $token = $this->tokens[$name] ?? null;
        return $token ? $token->value() : '';
    }
}
