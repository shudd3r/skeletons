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
use Shudd3r\PackageFiles\Token\Reader\Source\CachedValue;
use Shudd3r\PackageFiles\Tests\Doubles;


class CachedValueTest extends TestCase
{
    public function testCreate_IsDelegatedToWrappedSource()
    {
        $cached = new CachedValue($wrapped = new Doubles\FakeSource('value'));
        $this->assertSame($cached->create('test'), $wrapped->created);
    }

    public function testValueReadFromWrappedSourceIsCached()
    {
        $cached = new CachedValue($source = new Doubles\FakeSource('value'));
        $this->assertSame(0, $source->reads);
        $this->assertSame('value', $cached->value());
        $this->assertSame(1, $source->reads);
        $this->assertSame('value', $cached->value());
        $this->assertSame(1, $source->reads);
    }
}
