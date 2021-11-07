<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Processors\Processor;

use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Replacements\Token;


class FallbackProcessor implements Processor
{
    private Processor $primary;
    private Processor $fallback;

    public function __construct(Processor $primary, Processor $fallback)
    {
        $this->primary  = $primary;
        $this->fallback = $fallback;
    }

    public function process(Token $token): bool
    {
        return $this->primary->process($token) || $this->fallback->process($token);
    }
}
