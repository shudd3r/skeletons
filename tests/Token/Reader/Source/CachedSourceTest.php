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


class CachedSourceTest extends TestCase
{
    public function testValueMemoization()
    {
        $calls    = 0;
        $callback = function() use (&$calls) {
            $calls++;
            return 'source value';
        };
        $source = new Source\CachedSource(new Source\CallbackSource($callback));

        $this->assertSame(0, $calls);
        $this->assertSame('source value', $source->value());
        $this->assertSame(1, $calls);
        $this->assertSame('source value', $source->value());
        $this->assertSame(1, $calls);
    }
}
