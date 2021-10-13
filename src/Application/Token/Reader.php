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


abstract class Reader
{
    protected Replacements $replacements;

    private array $tokens;

    public function __construct(Replacements $replacements)
    {
        $this->replacements = $replacements;
    }

    public function token(): ?Token
    {
        $this->tokens ??= $this->tokens();
        return !in_array(null, $this->tokens) ? new Token\CompositeToken(...array_values($this->tokens)) : null;
    }

    public function tokenValues(): array
    {
        $this->tokens ??= $this->tokens();

        $values = [];
        foreach ($this->tokens as $name => $token) {
            $values[$name] = $token ? $token->value() : null;
        }

        return $values;
    }

    abstract protected function tokens(): array;
}
