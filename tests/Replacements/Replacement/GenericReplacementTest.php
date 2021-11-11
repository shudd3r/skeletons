<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Replacement\GenericReplacement;
use Shudd3r\Skeletons\Replacements\Reader\FallbackReader;
use Shudd3r\Skeletons\Replacements\Token\ValueToken;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Tests\Doubles;
use Closure;


class GenericReplacementTest extends TestCase
{
    private static Closure $default;

    public static function setUpBeforeClass(): void
    {
        self::$default = fn (RuntimeEnv $env, FallbackReader $fallback) => 'ok';
    }

    public function testInstanceWithDefaultValueOnly()
    {
        $replacement = new GenericReplacement(self::$default);

        $env      = new Doubles\FakeRuntimeEnv();
        $fallback = new Doubles\FakeFallbackReader();
        $this->assertSame('ok', $replacement->defaultValue($env, $fallback));
        $this->assertNull($replacement->inputPrompt());
        $this->assertNull($replacement->optionName());
        $this->assertEmpty($replacement->description());
        $this->assertEquals(new ValueToken('placeholder', 'value'), $replacement->token('placeholder', 'value'));
        $this->assertTrue($replacement->isValid('---anything---'));
    }

    public function testTokenCallback()
    {
        $createdToken = new ValueToken('foo', 'bar');
        $token        = fn (string $name, string $value) => $createdToken;
        $replacement  = new GenericReplacement(self::$default, $token);

        $this->assertSame($createdToken, $replacement->token('placeholder', 'value'));
    }

    public function testValidateCallback()
    {
        $validate    = fn (string $value) => $value === 'foo';
        $replacement = new GenericReplacement(self::$default, null, $validate);

        $this->assertTrue($replacement->isValid('foo'));
        $this->assertFalse($replacement->isValid('bar'));
    }

    public function testForInvalidValue_TokenMethod_ReturnsNull()
    {
        $createdToken = new ValueToken('foo', 'hardcoded');
        $token        = fn (string $name, string $value) => $createdToken;
        $validate     = fn (string $value) => $value === 'valid';
        $replacement  = new GenericReplacement(self::$default, $token, $validate);

        $this->assertSame($createdToken, $replacement->token('placeholder', 'valid'));
        $this->assertNull($replacement->token('placeholder', 'foo'));
    }

    public function testInputOptions()
    {
        $replacement = new GenericReplacement(self::$default, null, null, 'Give value', 'option', 'This is option');

        $this->assertSame('Give value', $replacement->inputPrompt());
        $this->assertSame('option', $replacement->optionName());
        $this->assertSame('This is option', $replacement->description());
    }

    public function testDescriptionFallback()
    {
        $replacement = new GenericReplacement(self::$default, null, null, 'Give value', 'option');

        $this->assertSame($replacement->inputPrompt(), $replacement->description());
    }
}
