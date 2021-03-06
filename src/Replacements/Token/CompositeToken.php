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


class CompositeToken implements Token
{
    private array   $tokens;
    private ?string $value = null;

    public function __construct(Token ...$tokens)
    {
        $this->tokens = $tokens;
    }

    public static function withValueToken(Token $valueToken, Token ...$tokens): self
    {
        $token = new self($valueToken, ...$tokens);
        $token->value = $valueToken->value();
        return $token;
    }

    public function replace(string $template): string
    {
        foreach ($this->tokens as $token) {
            $template = $token->replace($template);
        }

        return $template;
    }

    public function value(): ?string
    {
        return $this->value;
    }
}
