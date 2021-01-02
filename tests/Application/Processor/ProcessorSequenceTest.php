<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Token;
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
        $token    = new Doubles\FakeToken();

        $this->assertProcessorCalled(null, ...$processors);

        $this->assertTrue($sequence->process($token));
        $this->assertProcessorCalled($token, ...$processors);
    }

    public function testFailedSubProcess_ReturnsFailStatus()
    {
        $processors = [
            new Doubles\MockedProcessor(),
            new Doubles\MockedProcessor(),
            new Doubles\MockedProcessor(false),
            new Doubles\MockedProcessor()
        ];

        $sequence = new Processor\ProcessorSequence(...$processors);
        $token    = new Doubles\FakeToken();

        $this->assertFalse($sequence->process($token));
        $this->assertProcessorCalled($token, ...$processors);
    }

    private function assertProcessorCalled(?Token $token, Doubles\MockedProcessor ...$processors): void
    {
        foreach ($processors as $processor) {
            $this->assertSame($token, $processor->passedToken);
        }
    }
}
