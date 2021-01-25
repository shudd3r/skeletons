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


class CompositeValueToken extends ValueToken
{
    private array $subTokens;

    public function __construct(string $placeholder, string $value, Token ...$subTokens)
    {
        $this->subTokens = $subTokens;
        parent::__construct($placeholder, $value);
    }

    public function replacePlaceholders(string $template): string
    {
        $template = parent::replacePlaceholders($template);

        foreach ($this->subTokens as $token) {
            $template = $token->replacePlaceholders($template);
        }

        return $template;
    }
}
