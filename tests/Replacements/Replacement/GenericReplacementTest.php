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
use Shudd3r\Skeletons\Tests\Doubles\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token;


class GenericReplacementTest extends TestCase
{
    public function testWithoutOtherDataSourceDefined_TokenIsBuiltWithResolvedValue()
    {
        $resolvedValue = fn (Source $source) => $source->metaValueOf('placeholder');
        $replacement   = new GenericReplacement($resolvedValue);

        $source = Source::create(['placeholder' => 'value from source']);
        $this->assertToken('value from source', $replacement->token('foo', $source));
    }

    public function testWithValidationCallback_TokenValueIsValidated()
    {
        $resolvedValue = fn (Source $source) => $source->metaValueOf('placeholder');
        $isValid       = fn (string $value) => $value !== 'invalid value';
        $replacement   = new GenericReplacement($resolvedValue, $isValid);

        $source = Source::create(['placeholder' => 'invalid value']);
        $this->assertNull($replacement->token('foo', $source));
    }

    public function testWithTokenInstanceCallback_TokenIsCreatedWithThatCallback()
    {
        $resolvedValue = fn (Source $source) => $source->metaValueOf('placeholder');
        $isValid       = fn (string $value) => $value === 'valid value';
        $tokenInstance = fn (string $name, string $value) => new Token\BasicToken($name, 'modified+' . $value);
        $replacement   = new GenericReplacement($resolvedValue, $isValid, $tokenInstance);

        $source = Source::create(['placeholder' => 'valid value']);
        $this->assertToken('modified+valid value', $replacement->token('foo', $source));
    }

    private function assertToken(string $value, Token $token): void
    {
        $this->assertEquals(new Token\BasicToken('foo', $value), $token);
    }
}
