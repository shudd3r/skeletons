<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Token;


class ExpandedTokenProcessor implements Processor
{
    private Token     $token;
    private Processor $processor;

    public function __construct(Token $token, Processor $processor)
    {
        $this->token     = $token;
        $this->processor = $processor;
    }

    public function process(Token $token): void
    {
        $token = new Token\CompositeToken($token, $this->token);
        $this->processor->process($token);
    }
}
