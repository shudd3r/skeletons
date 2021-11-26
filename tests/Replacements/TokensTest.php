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
use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Tests\Doubles;


class TokensTest extends TestCase
{
    public function testForValidTokens_CompositeTokenMethod_ReturnCompositeToken()
    {
        $replacements = $this->replacements(['foo' => 'foo-value', 'bar' => 'valid', 'baz' => 'null']);
        $tokens       = new Tokens(new Replacements($replacements), new Doubles\FakeReader());

        $createToken = fn (string $name, Replacement $replacement) => $this->replacementToken($name, $replacement);
        $expected    = new Token\CompositeToken(...array_map($createToken, array_keys($replacements), $replacements));
        $this->assertEquals($expected, $tokens->compositeToken());
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
        return new Tokens(new Replacements($this->replacements($replacementValues)), new Doubles\FakeReader());
    }

    private function replacements(array $replacementValues): array
    {
        $create = fn (?string $value) => $value ? new Doubles\FakeReplacement($value) : null;
        return array_combine(array_keys($replacementValues), array_map($create, $replacementValues));
    }

    private function replacementToken(string $name, Replacement $replacement): ?Token
    {
        $value = $replacement->defaultValue(new Doubles\FakeRuntimeEnv(), new Doubles\FakeReader());
        return $replacement->token($name, $value);
    }
}
