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

use Shudd3r\Skeletons\Tests\Replacements\ReaderTests;
use Shudd3r\Skeletons\Replacements\Reader;


class InputReaderTest extends ReaderTests
{
    public function testWithoutInputValues_TokensAreBuiltWithDefaultReplacementValues()
    {
        $reader   = $this->reader([], []);
        $expected = $this->defaults();
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testWithoutInputValuesButWithExistingMetaData_TokensAreBuiltWithMetaDataValues()
    {
        $reader = $this->reader([], [], self::META_DATA);
        $this->assertTokenValues(self::META_DATA, $reader->tokens($this->replacements()));
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $reader   = $this->reader([], ['optBaz=baz (option)']);
        $expected = $this->defaults(['baz' => 'baz (option)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokenIsBuiltWithThatValue()
    {
        $reader   = $this->reader(['', 'baz (input)'], ['-i']);
        $expected = $this->defaults(['baz' => 'baz (input)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $reader   = $this->reader([], ['-i', 'optFoo=foo (option)']);
        $expected = $this->defaults(['foo' => 'foo (option)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testInvalidInteractiveInput_IsRetriedUntilValid()
    {
        $reader   = $this->reader(['invalid', 'invalid', 'finally valid'], ['-i', 'optFoo=foo (option)']);
        $expected = $this->defaults(['foo' => 'finally valid']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testInvalidInteractiveInput_AfterTwoRetries_UsesInvalidValueAndStopsIteration()
    {
        $reader   = $this->reader(['invalid', 'invalid', 'invalid', 'finally valid'], ['-i', 'optFoo=foo (option)']);
        $expected = ['foo' => null];
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testWithUnresolvedDefaultValues_TokenDefaultsComeFromFallbackTokenValues()
    {
        $reader   = $this->reader([], []);
        $expected = $this->defaults(['bar' => 'foo (default)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements(['bar'])));

        $reader   = $this->reader([], []);
        $expected = $this->defaults(['foo' => 'bar (default)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements(['foo'])));

        $reader   = $this->reader([], ['optBar=bar (option)']);
        $expected = $this->defaults(['foo' => 'bar (option)', 'bar' => 'bar (option)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements(['foo'])));

        $reader   = $this->reader(['foo (input)'], ['-i', 'optFoo=foo (option)']);
        $expected = $this->defaults(['foo' => 'foo (input)', 'bar' => 'foo (input)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements(['bar'])));
    }

    public function testWithCircularFallbackReferences_FallbackValuesResolveToEmptyString()
    {
        $reader   = $this->reader([], []);
        $expected = $this->defaults(['foo' => '', 'bar' => '']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements(['foo', 'bar'])));
    }

    protected function reader(array $inputs, array $options, array $metaData = []): Reader
    {
        return new Reader\InputReader($this->env($inputs, $metaData), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::REPLACEMENT_DEFAULTS, $override);
    }
}
