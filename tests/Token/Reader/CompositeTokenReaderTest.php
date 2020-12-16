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
use Shudd3r\PackageFiles\Tests\Doubles\FakeReaderV2;
use Shudd3r\PackageFiles\Tests\Doubles\FakeToken;
use Shudd3r\PackageFiles\Token\CompositeToken;
use Shudd3r\PackageFiles\Token\Reader\CompositeTokenReader;


class CompositeTokenReaderTest extends TestCase
{
    public function testReader_TokenMethod_ReturnsCompositeToken()
    {
        $reader   = new CompositeTokenReader(new FakeReaderV2('foo'), new FakeReaderV2('bar'));
        $expected = new CompositeToken(new FakeToken('foo'), new FakeToken('bar'));
        $this->assertEquals($expected, $reader->token());
    }

    public function testReaderWithInvalidComponent_TokenMethod_ReturnsNull()
    {
        $reader = new CompositeTokenReader(new FakeReaderV2('foo'), new FakeReaderV2(null), new FakeReaderV2('bar'));
        $this->assertNull($reader->token());
    }

    public function testReader_ValueMethod_ReturnsJsonString()
    {
        $reader   = new CompositeTokenReader($componentReader = new FakeReaderV2('foo'));
        $expected = json_encode([get_class($componentReader) => 'foo'], JSON_PRETTY_PRINT);
        $this->assertEquals($expected, $reader->value());
    }
}
