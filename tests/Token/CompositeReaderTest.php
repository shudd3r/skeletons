<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\CompositeReader;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;
use Exception;


class CompositeReaderTest extends TestCase
{
    public function testTokensAreBuiltWithProvidedCallbacks()
    {
        $callbacks = [fn() => new Doubles\FakeToken('foo'), fn() => new Doubles\FakeToken('bar')];
        $reader    = new CompositeReader(new Doubles\MockedTerminal(), ...$callbacks);

        $expected = new Token\TokenGroup(new Doubles\FakeToken('foo'), new Doubles\FakeToken('bar'));
        $this->assertEquals($expected, $reader->token());
    }

    public function testInvalidTokens()
    {
        $errorMessages = ['Invalid Foo token', 'Invalid Bar token'];

        $factories = [
            fn() => new Doubles\FakeToken('foo', $errorMessages[0]),
            fn() => new Doubles\FakeToken('bar', $errorMessages[1]),
            fn() => new Doubles\FakeToken('baz')
        ];

        $reader = new CompositeReader($output = new Doubles\MockedTerminal(), ...$factories);

        $this->expectException(Exception::class);
        $reader->token();

        $this->assertSame($errorMessages, $output->messagesSent);
        $this->assertNotEquals(0, $output->errorCode);
    }
}
