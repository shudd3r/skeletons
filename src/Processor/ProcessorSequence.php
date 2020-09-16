<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Processor;

use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Token;


class ProcessorSequence implements Processor
{
    /** @var Processor[] */
    private array $processors;

    public function __construct(Processor ...$processors)
    {
        $this->processors = $processors;
    }

    public function process(Token $token): void
    {
        foreach ($this->processors as $processor) {
            $processor->process($token);
        }
    }
}
