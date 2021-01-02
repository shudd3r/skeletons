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
use Shudd3r\PackageFiles\Application\Processor\ExpandedTokenProcessor;
use Shudd3r\PackageFiles\Application\Token\CompositeToken;
use Shudd3r\PackageFiles\Tests\Doubles;


class ExpandedTokenProcessorTest extends TestCase
{
    public function testSubsequentProcessorReceivesExpandedToken()
    {
        $newToken     = new Doubles\FakeToken('bar');
        $subProcessor = new Doubles\MockedProcessor();
        $processor    = new ExpandedTokenProcessor($newToken, $subProcessor);

        $token = new Doubles\FakeToken('foo');
        $this->assertTrue($processor->process($token));
        $this->assertEquals(new CompositeToken($token, $newToken), $subProcessor->passedToken);
    }
}
