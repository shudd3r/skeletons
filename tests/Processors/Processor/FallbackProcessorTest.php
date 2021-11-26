<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Processors\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Processors\Processor\FallbackProcessor;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\Tests\Doubles;


class FallbackProcessorTest extends TestCase
{
    public function testForSuccessfulPrimaryProcessing_FallbackProcessIsNotExecuted()
    {
        $fallback  = new Doubles\MockedProcessor(false);
        $processor = new FallbackProcessor(new Doubles\MockedProcessor(true), $fallback);
        $this->assertTrue($processor->process(new BasicToken('foo', 'bar')));
        $this->assertNull($fallback->passedToken());
    }

    public function testForFailedPrimaryProcessing_ReturnsStatusFromFallbackProcess()
    {
        $fallback  = new Doubles\MockedProcessor(false);
        $processor = new FallbackProcessor(new Doubles\MockedProcessor(false), $fallback);
        $this->assertFalse($processor->process($token = new BasicToken('foo', 'bar')));
        $this->assertSame($token, $fallback->passedToken());

        $fallback  = new Doubles\MockedProcessor(true);
        $processor = new FallbackProcessor(new Doubles\MockedProcessor(false), $fallback);
        $this->assertTrue($processor->process($token = new BasicToken('foo', 'bar')));
        $this->assertSame($token, $fallback->passedToken());
    }
}
