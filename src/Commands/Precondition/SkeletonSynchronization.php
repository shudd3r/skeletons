<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands\Precondition;

use Shudd3r\PackageFiles\Commands\Precondition;
use Shudd3r\PackageFiles\Replacements\Reader;
use Shudd3r\PackageFiles\Processor;


class SkeletonSynchronization implements Precondition
{
    private Reader    $reader;
    private Processor $processor;

    public function __construct(Reader $reader, Processor $processor)
    {
        $this->reader    = $reader;
        $this->processor = $processor;
    }

    public function isFulfilled(): bool
    {
        $token = $this->reader->token();
        return $token && $this->processor->process($token);
    }
}
