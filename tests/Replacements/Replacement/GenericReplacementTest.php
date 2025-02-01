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
    public function test_without_other_data_source_defined_Token_is_built_with_resolved_value(): void
    {
        $resolvedValue = fn (Source $source) => $source->metaValueOf('placeholder');
        $replacement   = new GenericReplacement($resolvedValue);

        $source = Source::create(['placeholder' => 'value from source']);
        $this->assertToken('value from source', $replacement, $source);
    }

    public function test_with_validation_callback_Token_value_is_validated(): void
    {
        $resolvedValue = fn (Source $source) => $source->metaValueOf('placeholder');
        $isValid       = fn (string $value) => $value !== 'invalid value';
        $replacement   = new GenericReplacement($resolvedValue, $isValid);

        $source = Source::create(['placeholder' => 'invalid value']);
        $this->assertNull($replacement->token('foo', $source));
    }

    public function test_with_Token_istance_callback_Token_is_created_with_that_callback(): void
    {
        $resolvedValue = fn (Source $source) => $source->metaValueOf('placeholder');
        $isValid       = fn (string $value) => $value === 'valid value';
        $tokenInstance = fn (string $name, string $value) => new Token\BasicToken($name, 'modified+' . $value);
        $replacement   = new GenericReplacement($resolvedValue, $isValid, $tokenInstance);

        $source = Source::create(['placeholder' => 'valid value']);
        $this->assertToken('modified+valid value', $replacement, $source);
    }

    private function assertToken(string $value, GenericReplacement $replacement, Source $source): void
    {
        $token = $replacement->token('foo', $source);
        $this->assertEquals(new Token\BasicToken('foo', $value), $token);
    }
}
