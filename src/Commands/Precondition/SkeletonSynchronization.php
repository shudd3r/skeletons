<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Precondition;

use Shudd3r\Skeletons\Commands\Precondition;
use Shudd3r\Skeletons\Rework\Replacements\Tokens;
use Shudd3r\Skeletons\Processors\Processor;


class SkeletonSynchronization implements Precondition
{
    private Tokens    $tokens;
    private Processor $processor;

    public function __construct(Tokens $tokens, Processor $processor)
    {
        $this->tokens    = $tokens;
        $this->processor = $processor;
    }

    public function isFulfilled(): bool
    {
        $token = $this->tokens->compositeToken();
        return $token && $this->processor->process($token);
    }
}
