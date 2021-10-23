<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Replacements\Reader;

use Shudd3r\PackageFiles\Tests\Replacements\ReaderTests;
use Shudd3r\PackageFiles\Replacements\Reader;


class UpdateReaderTest extends ReaderTests
{
    public function testWithoutInputValues_TokensAreBuiltWithMetaData()
    {
        $reader = $this->reader([], []);
        $this->assertTokenValues($reader, $this->defaults());
    }

    public function testWithOptionValues_TokensAreBuiltWithMatchingOptionValue()
    {
        $reader = $this->reader([], ['optBar' => 'bar (option)']);
        $this->assertTokenValues($reader, $this->defaults(['bar' => 'bar (option)']));
    }

    public function testWhenNotEmptyInteractiveInputValueIsGiven_TokensAreBuiltWithThatValue()
    {
        $reader = $this->reader(['', 'baz (input)'], ['i' => true]);
        $this->assertTokenValues($reader, $this->defaults(['baz' => 'baz (input)']));
    }

    public function testOptionValue_BecomesDefaultForEmptyInput()
    {
        $reader = $this->reader(['', ''], ['i' => true, 'optFoo' => 'foo (option)']);
        $this->assertTokenValues($reader, $this->defaults(['foo' => 'foo (option)']));
    }

    protected function reader(array $inputs, array $options, array $removeDefaults = []): Reader
    {
        $replacements = $this->replacements($removeDefaults);
        return new Reader\UpdateReader($replacements, $this->env($inputs, self::META_DATA), $options);
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::META_DATA, $override);
    }
}
