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


class ValidationReaderTest extends ReaderTests
{
    /**
     * @dataProvider possibleReaderSetups
     * @param Reader $reader
     * @param array  $removeDefaults
     */
    public function testTokensAreAlwaysBuiltWithMetaData(Reader $reader, array $removeDefaults = [])
    {
        $replacements = $this->replacements($removeDefaults);
        $expected     = $this->defaults();
        $this->assertTokenValues($expected, $reader->tokens($replacements));
    }

    public function possibleReaderSetups(): array
    {
        return [
            'no input'        => [$this->reader([], [])],
            'active fallback' => [$this->reader([], []), ['foo']],
            'cli option'      => [$this->reader([], ['optFoo' => 'foo (option)'])],
            'terminal input'  => [$this->reader(['foo (input)'], [])],
            'terminal & cli'  => [$this->reader(['foo (input)'], ['optBar' => 'bar (option)'])],
        ];
    }

    protected function reader(array $inputs, array $options): Reader
    {
        return new Reader\ValidationReader($this->env($inputs, self::META_DATA), $this->args($options));
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::META_DATA, $override);
    }
}
