<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Command\TokenProcessor;
use Shudd3r\PackageFiles\Tests\Doubles;


class TokenProcessorTest extends TestCase
{
    public function testTokensArePassedToProcessor()
    {
        $reader    = new Doubles\FakeReader();
        $processor = new Doubles\MockedProcessor();
        $terminal  = new Doubles\MockedTerminal();
        $command   = new TokenProcessor($reader, $processor, $terminal);

        $this->assertInstanceOf(TokenProcessor::class, $command);

        $command->execute();
        $this->assertSame($reader->token(), $processor->passedToken);
    }

    public function testUnresolvedTokensStopExecution()
    {
        $reader    = new Doubles\FakeReader(null);
        $processor = new Doubles\MockedProcessor();
        $terminal  = new Doubles\MockedTerminal();
        $command   = new TokenProcessor($reader, $processor, $terminal);

        $command->execute();
        $this->assertNull($processor->passedToken);
    }
}
