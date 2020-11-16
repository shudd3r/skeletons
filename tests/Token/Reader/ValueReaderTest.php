<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class ValueReaderTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = new Doubles\FakeValueReader('foo');
        $this->assertInstanceOf(Token\Reader::class, $reader);
        $this->assertInstanceOf(Token\Reader\Source::class, $reader);
        $this->assertInstanceOf(Token\Reader\ValueReader::class, $reader);
    }

    public function testValue_ReturnsValueFromGivenSource()
    {
        $reader = new Doubles\FakeValueReader('some value');
        $this->assertSame('some value', $reader->value());
    }

    public function testToken_ReturnsTokenFromGivenSource()
    {
        $reader = new Doubles\FakeValueReader('foo');
        $this->assertEquals(new Doubles\FakeToken('foo'), $reader->token());
    }

    public function testInvalidTokenValue_Token_ReturnsNull()
    {
        $reader = new Doubles\FakeValueReader(null);
        $this->assertNull($reader->token());
        $this->assertSame(['exception message'], $reader->output->messagesSent);
        $this->assertSame(1, $reader->output->errorCode);
    }

    public function testSourceValueIsCached_Value_ReadsSourceOnce()
    {
        $reader = new Doubles\FakeValueReader('some value');

        $this->assertSame(0, $reader->fakeSource->reads);
        $reader->value();
        $this->assertSame(1,$reader->fakeSource->reads);
        $reader->value();
        $this->assertSame(1, $reader->fakeSource->reads);
    }

    public function testSourceValueIsCached_Token_ReadsSourceOnce()
    {
        $reader = new Doubles\FakeValueReader('some value');

        $this->assertSame(0, $reader->fakeSource->reads);
        $reader->token();
        $this->assertSame(1, $reader->fakeSource->reads);
        $reader->token();
        $this->assertSame(1, $reader->fakeSource->reads);
    }
}
