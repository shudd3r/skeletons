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


class TokensReaderTest extends TestCase
{
    public function testTokensAreBuiltWithProvidedCallbacks()
    {
        $callbacks = [new Doubles\FakeReader('foo', '{a}'), new Doubles\FakeReader('bar', '{b}')];
        $reader    = new Token\Reader\TokensReader(new Doubles\MockedTerminal(), ...$callbacks);

        $expected = new Token\CompositeToken(
            Doubles\FakeToken::withPlaceholder('{a}', 'foo'),
            Doubles\FakeToken::withPlaceholder('{b}', 'bar')
        );
        $this->assertEquals($expected, $reader->token());
    }

    public function testInvalidTokens()
    {
        $factories = [
            new Doubles\FakeReader('foo'),
            new Doubles\FakeReader(null),
            new Doubles\FakeReader('bar')
        ];

        $reader = new Token\Reader\TokensReader($output = new Doubles\MockedTerminal(), ...$factories);

        $this->assertNull($reader->token());
        $this->assertSame(['Cannot process unresolved tokens'], $output->messagesSent);
        $this->assertNotEquals(0, $output->errorCode);
    }
}
