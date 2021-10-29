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
        $reader = $this->reader([], []);
        $this->assertTokenValues($reader, $this->defaults());
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $reader = $this->reader([], ['optBaz' => 'baz (option)']);
        $this->assertTokenValues($reader, $this->defaults(['baz' => 'baz (option)']));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokenIsBuiltWithThatValue()
    {
        $reader = $this->reader(['', 'baz (input)'], ['i' => true]);
        $this->assertTokenValues($reader, $this->defaults(['baz' => 'baz (input)']));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $reader = $this->reader(['', ''], ['i' => true, 'optFoo' => 'foo (option)']);
        $this->assertTokenValues($reader, $this->defaults(['foo' => 'foo (option)']));
    }

    public function testWithUnresolvedDefaultValues_TokenDefaultsComeFromFallbackTokenValues()
    {
        $reader = $this->reader([], [], ['bar']);
        $this->assertTokenValues($reader, $this->defaults(['bar' => 'foo (default)']));

        $reader = $this->reader([], [], ['foo']);
        $this->assertTokenValues($reader, $this->defaults(['foo' => 'bar (default)']));

        $reader = $this->reader([], ['optBar' => 'bar (option)'], ['foo']);
        $this->assertTokenValues($reader, $this->defaults(['foo' => 'bar (option)', 'bar' => 'bar (option)']));

        $reader = $this->reader(['foo (input)'], ['i' => true, 'optFoo' => 'foo (option)'], ['bar']);
        $this->assertTokenValues($reader, $this->defaults(['foo' => 'foo (input)', 'bar' => 'foo (input)']));
    }

    public function testWithCircularFallbackReferences_FallbackValuesResolveToEmptyString()
    {
        $reader = $this->reader([], [], ['foo', 'bar']);
        $this->assertTokenValues($reader, $this->defaults(['foo' => '', 'bar' => '']));
    }

    protected function reader(array $inputs, array $options, array $removeDefaults = []): Reader
    {
        $replacements = $this->replacements($removeDefaults);
        return new Reader\InitialReader($replacements, $this->env($inputs), $options);
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::REPLACEMENT_DEFAULTS, $override);
    }
}
