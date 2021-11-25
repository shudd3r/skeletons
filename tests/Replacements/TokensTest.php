<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Tokens;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Tests\Doubles;


class TokensTest extends TestCase
{
    public function testForValidTokens_CompositeTokenMethod_ReturnCompositeToken()
    {
        $tokens = $this->tokens(['foo' => 'foo-value', 'bar' => 'valid', 'baz' => 'null']);

        $expectedToken = new Token\CompositeToken(...[
            new Token\ValueToken('foo', 'foo-value'),
            new Token\ValueToken('bar', 'valid'),
            new Doubles\FakeToken('baz', null)
        ]);
        $this->assertEquals($expectedToken, $tokens->compositeToken());
    }

    public function testForInvalidTokens_CompositeTokenMethod_ReturnsNull()
    {
        $tokens = $this->tokens(['foo' => 'bar', 'bar' => 'invalid', 'baz' => 'baz-value']);
        $this->assertNull($tokens->compositeToken());
    }

    public function testForValidTokens_PlaceholderValuesMethod_ReturnListWithNotNullTokenValues()
    {
        $tokens   = $this->tokens(['foo' => 'foo-value', 'bar' => 'valid', 'baz' => 'baz-value']);
        $expected = ['foo' => 'foo-value', 'bar' => 'valid', 'baz' => 'baz-value'];
        $this->assertSame($expected, $tokens->placeholderValues());

        $tokens   = $this->tokens(['foo' => 'foo-value', 'bar' => 'null', 'baz' => 'baz-value']);
        $expected = ['foo' => 'foo-value', 'baz' => 'baz-value'];
        $this->assertSame($expected, $tokens->placeholderValues());
    }

    public function testForInvalidTokens_CompositeTokenMethod_ReturnsListWithNullValues()
    {
        $tokens = $this->tokens(['foo' => 'bar', 'bar' => 'invalid', 'baz' => 'baz-value']);
        $this->assertNull($tokens->compositeToken());
        $this->assertSame(['foo' => 'bar', 'bar' => null, 'baz' => 'baz-value'], $tokens->placeholderValues());
    }

    private function tokens(array $replacementValues): Tokens
    {
        $create       = fn (?string $value) => $value ? new Doubles\FakeReplacement($value) : null;
        $replacements = array_combine(array_keys($replacementValues), array_map($create, $replacementValues));

        return new Tokens(new Replacements($replacements), new Doubles\FakeReader());
    }
}
