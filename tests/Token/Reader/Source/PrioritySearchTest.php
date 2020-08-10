<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\Source;


class PrioritySearchTest extends TestCase
{
    public function testSourceComposedWithGivenOtherSources_ReturnsFirstNonEmptyValue()
    {
        $source = $this->source('', '', 'first', 'second', '');
        $this->assertSame('first', $source->value());
    }

    public function testUnresolvedValueFromGivenSources_ReturnsEmptyString()
    {
        $this->assertSame('', $this->source('', '', '')->value());
    }

    private function source(string ...$values): Source\PrioritySearch
    {
        $sources = [];
        foreach ($values as $value) {
            $sources[] = new Source\CallbackSource(fn() => $value);
        }

        return new Source\PrioritySearch(...$sources);
    }
}
