<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates\Template;

use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Replacements\Token;


class BasicTemplate implements Template
{
    private string $contents;

    public function __construct(string $contents)
    {
        $this->contents = $contents;
    }

    public function render(Token $token): string
    {
        return $token->replace($this->contents);
    }
}
