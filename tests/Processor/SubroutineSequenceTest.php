<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class ProcessorSequenceTest extends TestCase
{
    public function testAllProcessorsAreCalled()
    {
        $processors = [
            new Doubles\MockedProcessor(),
            new Doubles\MockedProcessor(),
            new Doubles\MockedProcessor(),
            new Doubles\MockedProcessor()
        ];

        $sequence = new Processor\ProcessorSequence(...$processors);
        $this->assertProcessorCalled(null, ...$processors);

        $sequence->process($tokens = new Doubles\FakeToken());
        $this->assertProcessorCalled($tokens, ...$processors);
    }

    private function assertProcessorCalled(?Token $token, Doubles\MockedProcessor ...$processors): void
    {
        foreach ($processors as $processor) {
            $this->assertSame($token, $processor->passedToken);
        }
    }
}
