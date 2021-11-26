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


class InitialReaderTest extends ReaderTests
{
    public function testWithoutInputValues_TokensAreBuiltWithDefaultReplacementValues()
    {
        $reader       = $this->reader([], []);
        $replacements = $this->replacements();
        $expected     = $this->defaults();
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $reader       = $this->reader([], ['optBaz=baz (option)']);
        $replacements = $this->replacements();
        $expected     = $this->defaults(['baz' => 'baz (option)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokenIsBuiltWithThatValue()
    {
        $reader       = $this->reader(['', 'baz (input)'], ['-i']);
        $replacements = $this->replacements();
        $expected     = $this->defaults(['baz' => 'baz (input)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $reader       = $this->reader([], ['-i', 'optFoo=foo (option)']);
        $replacements = $this->replacements();
        $expected     = $this->defaults(['foo' => 'foo (option)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testInvalidInteractiveInput_IsRetriedUntilValid()
    {
        $reader       = $this->reader(['invalid', 'invalid', 'finally valid'], ['-i', 'optFoo=foo (option)']);
        $replacements = $this->replacements();
        $expected     = $this->defaults(['foo' => 'finally valid']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testInvalidInteractiveInput_AfterTwoRetries_UsesInvalidValueAndStopsIteration()
    {
        $inputs       = ['invalid', 'invalid', 'invalid', 'finally valid'];
        $reader       = $this->reader($inputs, ['-i', 'optFoo=foo (option)']);
        $replacements = $this->replacements();
        $expected     = ['foo' => null];
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testWithUnresolvedDefaultValues_TokenDefaultsComeFromFallbackTokenValues()
    {
        $reader       = $this->reader([], []);
        $replacements = $this->replacements(['bar']);
        $expected     = $this->defaults(['bar' => 'foo (default)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));

        $reader       = $this->reader([], []);
        $replacements = $this->replacements(['foo']);
        $expected     = $this->defaults(['foo' => 'bar (default)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));

        $reader       = $this->reader([], ['optBar=bar (option)']);
        $replacements = $this->replacements(['foo']);
        $expected     = $this->defaults(['foo' => 'bar (option)', 'bar' => 'bar (option)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));

        $reader       = $this->reader(['foo (input)'], ['-i', 'optFoo=foo (option)']);
        $replacements = $this->replacements(['bar']);
        $expected     = $this->defaults(['foo' => 'foo (input)', 'bar' => 'foo (input)']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function testWithCircularFallbackReferences_FallbackValuesResolveToEmptyString()
    {
        $reader       = $this->reader([], []);
        $replacements = $this->replacements(['foo', 'bar']);
        $expected     = $this->defaults(['foo' => '', 'bar' => '']);
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    protected function reader(array $inputs, array $options, array $metaData = []): Reader
    {
        return new Reader\InitialReader($this->env($inputs), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::REPLACEMENT_DEFAULTS, $override);
    }
}
