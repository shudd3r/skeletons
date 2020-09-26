<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Command;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Command\TokenProcessor;
use Shudd3r\PackageFiles\Tests\Doubles;
use Exception;


class TokenProcessorTest extends TestCase
{
    public function testInstantiation()
    {
        $command = new TokenProcessor(new Doubles\FakeReader(), new Doubles\MockedProcessor());
        $this->assertInstanceOf(TokenProcessor::class, $command);
    }

    public function testPropertiesArePassedToProcessor()
    {
        $reader     = new Doubles\FakeReader();
        $processor = new Doubles\MockedProcessor();
        $command    = new TokenProcessor($reader, $processor);

        $command->execute();
        $this->assertEquals($reader->token(), $processor->passedToken);
    }

    public function testUnresolvedPropertiesStopExecution()
    {
        $command = new TokenProcessor(new Doubles\FakeReader('value', 'exception'), new Doubles\MockedProcessor());

        $this->expectException(Exception::class);
        $command->execute();
    }
}
