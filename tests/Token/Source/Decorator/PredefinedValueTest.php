<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Source\Decorator;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Source\Decorator\PredefinedValue;
use Shudd3r\PackageFiles\Tests\Doubles;


class PredefinedValueTest extends TestCase
{
    public function testCreate_IsDelegatedToWrappedSource()
    {
        $wrapped = new Doubles\FakeSource('');
        $source  = new PredefinedValue('some string', $wrapped);
        $this->assertSame($source->token('test'), $wrapped->created);
    }

    public function testValue_ReturnsConstructorProperty()
    {
        $source = new PredefinedValue('some string', new Doubles\FakeSource(''));
        $this->assertSame('some string', $source->value());
    }
}
