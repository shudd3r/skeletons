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
use Shudd3r\Skeletons\Replacements\Reader\UpdateReader;


class UpdateReaderTest extends TokensTests
{
    public function testWithoutInputValues_TokensAreBuiltWithMetaData()
    {
        $tokens = $this->tokens([], []);
        $this->assertTokenValues($tokens, $this->defaults());
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $tokens = $this->tokens([], ['optBar=bar (option)']);
        $this->assertTokenValues($tokens, $this->defaults(['bar' => 'bar (option)']));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokensAreBuiltWithThatValue()
    {
        $tokens = $this->tokens(['', 'baz (input)'], ['-i']);
        $this->assertTokenValues($tokens, $this->defaults(['baz' => 'baz (input)']));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $tokens = $this->tokens(['', ''], ['-i', 'optFoo=foo (option)']);
        $this->assertTokenValues($tokens, $this->defaults(['foo' => 'foo (option)']));
    }

    protected function reader(array $inputs, array $options): UpdateReader
    {
        return new UpdateReader($this->env($inputs, self::META_DATA), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::META_DATA, $override);
    }
}
