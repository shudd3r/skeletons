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
use Shudd3r\PackageFiles\Token\Reader\Source\PredefinedString;


class PredefinedStringTest extends TestCase
{
    public function testValue_ReturnsConstructorProperty()
    {
        $source = new PredefinedString('some string');
        $this->assertSame('some string', $source->value());
    }
}
