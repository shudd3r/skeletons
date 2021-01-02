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


class ProcessorSequence implements Processor
{
    /** @var Processor[] */
    private array $processors;

    public function __construct(Processor ...$processors)
    {
        $this->processors = $processors;
    }

    public function process(Token $token): bool
    {
        $status = true;
        foreach ($this->processors as $processor) {
            $status = $processor->process($token) && $status;
        }

        return $status;
    }
}
