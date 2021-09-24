<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Command\Precondition;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Command\Precondition\SkeletonSynchronization;
use Shudd3r\PackageFiles\Tests\Doubles;


class SkeletonSynchronizationTest extends TestCase
{
    public function testUnresolvedToken_ReturnsFalse()
    {
        $reader       = new Doubles\FakeReader(null);
        $processor    = new Doubles\MockedProcessor();
        $precondition = new SkeletonSynchronization($reader, $processor);
        $this->assertFalse($precondition->isFulfilled());
    }

    public function testResolvedToken_ReturnsStatusFromProcessor()
    {
        $reader       = new Doubles\FakeReader();
        $processor    = new Doubles\MockedProcessor(true);
        $precondition = new SkeletonSynchronization($reader, $processor);
        $this->assertTrue($precondition->isFulfilled());
        $this->assertEquals($reader->token(), $processor->passedToken);

        $reader       = new Doubles\FakeReader();
        $processor    = new Doubles\MockedProcessor(false);
        $precondition = new SkeletonSynchronization($reader, $processor);
        $this->assertFalse($precondition->isFulfilled());
        $this->assertEquals($reader->token(), $processor->passedToken);
    }
}
