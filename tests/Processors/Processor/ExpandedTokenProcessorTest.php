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
use Shudd3r\Skeletons\Processors\Processor\ExpandedTokenProcessor;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Tests\Doubles;


class ExpandedTokenProcessorTest extends TestCase
{
    public function testSubsequentProcessor_ReceivesExpandedToken()
    {
        $newToken     = new Token\BasicToken('foo', 'one');
        $subProcessor = new Doubles\MockedProcessor();
        $processor    = new ExpandedTokenProcessor($newToken, $subProcessor);

        $this->assertTrue($processor->process($composedToken = new Token\BasicToken('bar', 'two')));
        $this->assertEquals(new Token\CompositeToken($composedToken, $newToken), $subProcessor->passedToken());
    }
}
