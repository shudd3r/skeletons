<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Reader\CompositeTokenReader;
use Shudd3r\PackageFiles\Application\Token\CompositeToken;
use Shudd3r\PackageFiles\Tests\Doubles\FakeReader;
use Shudd3r\PackageFiles\Tests\Doubles\FakeToken;


class CompositeTokenReaderTest extends TestCase
{
    public function testReader_TokenMethod_ReturnsCompositeToken()
    {
        $readers = [
            'foo-token' => new FakeReader('foo'),
            'bar-token' => new FakeReader('bar')
        ];

        $reader   = new CompositeTokenReader($readers);
        $expected = new CompositeToken(
            new FakeToken('foo', 'myTokens.foo-token'),
            new FakeToken('bar', 'myTokens.bar-token')
        );
        $this->assertEquals($expected, $reader->token('myTokens'));
    }

    public function testReaderWithInvalidComponent_TokenMethod_ReturnsNull()
    {
        $reader = new CompositeTokenReader([new FakeReader('foo'), new FakeReader(null), new FakeReader('bar')]);
        $this->assertNull($reader->token());
    }

    public function testReader_ValueMethod_ReturnsJsonString()
    {
        $reader   = new CompositeTokenReader(['foo.placeholder' => new FakeReader('foo')]);
        $expected = json_encode(['foo.placeholder' => 'foo'], JSON_PRETTY_PRINT);
        $this->assertEquals($expected, $reader->value());
    }
}
