<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Replacements\Reader;

use Shudd3r\PackageFiles\Tests\Application\Replacements\ReaderTests;
use Shudd3r\PackageFiles\Application\Replacements\Reader;


class ValidationReaderTest extends ReaderTests
{
    /**
     * @dataProvider possibleReaderSetups
     * @param Reader $reader
     */
    public function testTokensAreAlwaysBuiltWithMetaData(Reader $reader)
    {
        $this->assertTokenValues($reader, $this->defaults());
    }

    public function possibleReaderSetups(): array
    {
        return [
            'no input'        => [$this->reader([], [])],
            'active fallback' => [$this->reader([], [], ['foo'])],
            'cli option'      => [$this->reader([], ['optFoo' => 'foo (option)'])],
            'terminal input'  => [$this->reader(['foo (input)'], [])],
            'terminal & cli'  => [$this->reader(['foo (input)'], ['optBar' => 'bar (option)'])],
        ];
    }

    protected function reader(array $inputs, array $options, array $removeDefaults = []): Reader
    {
        $replacements = $this->replacements($removeDefaults);
        return new Reader\ValidationReader($replacements, $this->env($inputs, self::META_DATA), $options);
    }

    protected function defaults(array $override = []): array
    {
        return array_merge(self::META_DATA, $override);
    }
}
