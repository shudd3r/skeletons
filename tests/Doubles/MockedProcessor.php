<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Replacements\Token;


class MockedProcessor implements Processor
{
    private ?Token $passedToken = null;
    private bool   $status;

    public function __construct(bool $status = true)
    {
        $this->status = $status;
    }

    public function process(Token $token): bool
    {
        $this->passedToken = $token;
        return $this->status;
    }

    public function passedToken(): ?Token
    {
        return $this->passedToken;
    }
}
