<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Reader;

use Shudd3r\Skeletons\Tests\Replacements\TokensTests;
use Shudd3r\Skeletons\Replacements\Reader\InitialReader;


class InitialReaderTest extends TokensTests
{
    public function testWithoutInputValues_TokensAreBuiltWithDefaultReplacementValues()
    {
        $tokens = $this->tokens([], []);
        $this->assertTokenValues($tokens, $this->defaults());
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $tokens = $this->tokens([], ['optBaz=baz (option)']);
        $this->assertTokenValues($tokens, $this->defaults(['baz' => 'baz (option)']));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokenIsBuiltWithThatValue()
    {
        $tokens = $this->tokens(['', 'baz (input)'], ['-i']);
        $this->assertTokenValues($tokens, $this->defaults(['baz' => 'baz (input)']));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $tokens = $this->tokens([], ['-i', 'optFoo=foo (option)']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => 'foo (option)']));
    }

    public function testInvalidInteractiveInput_IsRetriedUntilValid()
    {
        $tokens = $this->tokens(['invalid', 'invalid', 'finally valid'], ['-i', 'optFoo=foo (option)']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => 'finally valid']));
    }

    public function testInvalidInteractiveInput_AfterTwoRetriesUsesInvalidValue()
    {
        $inputs = ['invalid', 'invalid', 'invalid', 'finally valid'];
        $tokens = $this->tokens($inputs, ['-i', 'optFoo=foo (option)']);
        $this->assertNull($tokens->compositeToken());
    }

    public function testWithUnresolvedDefaultValues_TokenDefaultsComeFromFallbackTokenValues()
    {
        $tokens = $this->tokens([], [], ['bar']);
        $this->assertTokenValues($tokens, $this->defaults(['bar' => 'foo (default)']));

        $tokens = $this->tokens([], [], ['foo']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => 'bar (default)']));

        $tokens = $this->tokens([], ['optBar=bar (option)'], ['foo']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => 'bar (option)', 'bar' => 'bar (option)']));

        $tokens = $this->tokens(['foo (input)'], ['-i', 'optFoo=foo (option)'], ['bar']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => 'foo (input)', 'bar' => 'foo (input)']));
    }

    public function testWithCircularFallbackReferences_FallbackValuesResolveToEmptyString()
    {
        $tokens = $this->tokens([], [], ['foo', 'bar']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => '', 'bar' => '']));
    }

    protected function reader(array $inputs, array $options): InitialReader
    {
        return new InitialReader($this->env($inputs), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::REPLACEMENT_DEFAULTS, $override);
    }
}
