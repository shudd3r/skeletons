<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Commands\Precondition;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Commands\Precondition\SkeletonSynchronization;
use Shudd3r\Skeletons\Tests\Doubles;


class SkeletonSynchronizationTest extends TestCase
{
    public function test_unresolved_token_returns_false():void
    {
        $tokens       = new Doubles\FakeTokens(false);
        $processor    = new Doubles\MockedProcessor(true);
        $precondition = new SkeletonSynchronization($tokens, $processor);
        $this->assertFalse($precondition->isFulfilled());
    }

    public function test_resolved_token_returns_status_from_processor(): void
    {
        $tokens       = new Doubles\FakeTokens(true);
        $processor    = new Doubles\MockedProcessor(true);
        $precondition = new SkeletonSynchronization($tokens, $processor);
        $this->assertTrue($precondition->isFulfilled());
        $this->assertEquals($tokens->compositeToken(), $processor->passedToken());

        $processor    = new Doubles\MockedProcessor(false);
        $precondition = new SkeletonSynchronization($tokens, $processor);
        $this->assertFalse($precondition->isFulfilled());
        $this->assertEquals($tokens->compositeToken(), $processor->passedToken());
    }
}
