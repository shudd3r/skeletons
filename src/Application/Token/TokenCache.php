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


class TokenCache
{
    private array $tokens;

    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
    }

    public function add(string $name, Token $token): void
    {
        $this->tokens[$name] = $token;
    }

    public function token(string $name): ?Token
    {
        return $this->tokens[$name] ?? null;
    }
}
