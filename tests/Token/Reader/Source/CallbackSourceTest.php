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
use Shudd3r\PackageFiles\Token\Reader\Source\CallbackSource;


class CallbackSourceTest extends TestCase
{
    public function testValue_ReturnsCallbackResult()
    {
        $source = new CallbackSource(fn() => 'some string');
        $this->assertSame('some string', $source->value());
    }
}