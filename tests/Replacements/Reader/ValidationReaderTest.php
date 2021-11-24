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
use Shudd3r\Skeletons\Replacements\Reader\ValidationReader;
use Shudd3r\Skeletons\Replacements\Tokens;


class ValidationReaderTest extends TokensTests
{
    /**
     * @dataProvider possibleReaderSetups
     * @param Tokens $tokens
     */
    public function testTokensAreAlwaysBuiltWithMetaData(Tokens $tokens)
    {
        $this->assertTokenValues($tokens, $this->defaults());
    }

    public function possibleReaderSetups(): array
    {
        return [
            'no input'        => [$this->tokens([], [])],
            'active fallback' => [$this->tokens([], [], ['foo'])],
            'cli option'      => [$this->tokens([], ['optFoo' => 'foo (option)'])],
            'terminal input'  => [$this->tokens(['foo (input)'], [])],
            'terminal & cli'  => [$this->tokens(['foo (input)'], ['optBar' => 'bar (option)'])],
        ];
    }

    protected function reader(array $inputs, array $options): ValidationReader
    {
        return new ValidationReader($this->env($inputs, self::META_DATA), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::META_DATA, $override);
    }
}
