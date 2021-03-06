<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Commands\Command;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Commands\Command\ProcessTokens;
use Shudd3r\Skeletons\Tests\Doubles;


class ProcessTokensTest extends TestCase
{
    public function testResolvedTokens_ArePassedToProcessor()
    {
        $tokens    = new Doubles\FakeTokens(true);
        $processor = new Doubles\MockedProcessor();
        $terminal  = new Doubles\MockedTerminal();
        $command   = new ProcessTokens($tokens, $processor, $terminal);

        $command->execute();
        $this->assertEquals($tokens->compositeToken(), $processor->passedToken());
    }

    public function testUnresolvedTokens_ExecutionIsStopped()
    {
        $tokens    = new Doubles\FakeTokens(false);
        $processor = new Doubles\MockedProcessor();
        $terminal  = new Doubles\MockedTerminal();
        $command   = new ProcessTokens($tokens, $processor, $terminal);

        $command->execute();
        $this->assertNull($processor->passedToken());
    }
}
