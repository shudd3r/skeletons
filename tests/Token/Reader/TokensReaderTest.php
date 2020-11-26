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
    public function testTokensAreBuiltWithProvidedSources()
    {
        $sources = [new Doubles\FakeSource('foo'), new Doubles\FakeSource('bar')];
        $reader  = new Token\Reader\TokensReader(new Doubles\MockedTerminal(), ...$sources);

        $expected = new Token\CompositeToken(new Doubles\FakeToken('foo'), new Doubles\FakeToken('bar'));
        $this->assertEquals($expected, $reader->token());
    }

    public function testInvalidTokens()
    {
        $sources = [new Doubles\FakeSource('foo'), new Doubles\FakeSource(null), new Doubles\FakeSource('bar')];
        $output  = new Doubles\MockedTerminal();
        $reader  = new Token\Reader\TokensReader($output, ...$sources);

        $this->assertNull($reader->token());
        $this->assertSame(['Cannot process unresolved tokens'], $output->messagesSent);
        $this->assertNotEquals(0, $output->errorCode);
    }
}
