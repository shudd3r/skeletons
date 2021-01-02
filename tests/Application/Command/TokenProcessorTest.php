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
    public function testInstantiation()
    {
        $command = new TokenProcessor(new Doubles\FakeReader(), new Doubles\MockedProcessor());
        $this->assertInstanceOf(TokenProcessor::class, $command);
    }

    public function testPropertiesArePassedToProcessor()
    {
        $reader    = new Doubles\FakeReader();
        $processor = new Doubles\MockedProcessor();
        $command   = new TokenProcessor($reader, $processor);

        $command->execute();
        $this->assertSame($reader->token(), $processor->passedToken);
    }

    public function testUnresolvedPropertiesStopExecution()
    {
        $command = new TokenProcessor(new Doubles\FakeReader(null), $processor = new Doubles\MockedProcessor());

        $command->execute();
        $this->assertNull($processor->passedToken);
    }
}