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


class UpdateReaderTest extends ReaderTests
{
    public function testWithoutInputValues_TokensAreBuiltWithMetaData()
    {
        $reader   = $this->reader([], []);
        $expected = $this->defaults();
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testWithoutNeitherMetaDataNorInput_TokensAreBuiltWithReplacementDefaultValues()
    {
        $metaData = self::META_DATA;
        unset($metaData['foo']);

        $reader   = $this->reader([], [], $metaData);
        $expected = $this->defaults(['foo' => 'foo (default)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $reader   = $this->reader([], ['optBar=bar (option)']);
        $expected = $this->defaults(['bar' => 'bar (option)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokensAreBuiltWithThatValue()
    {
        $reader   = $this->reader(['', 'baz (input)'], ['-i']);
        $expected = $this->defaults(['baz' => 'baz (input)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $reader   = $this->reader(['', ''], ['-i', 'optFoo=foo (option)']);
        $expected = $this->defaults(['foo' => 'foo (option)']);
        $this->assertTokenValues($expected, $reader->tokens($this->replacements()));
    }

    protected function reader(array $inputs, array $options, array $metaData = []): Reader
    {
        return new Reader\UpdateReader($this->env($inputs, $metaData ?: self::META_DATA), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::META_DATA, $override);
    }
}
