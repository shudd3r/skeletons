<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Reader\SubTokenReader;
use Shudd3r\PackageFiles\Application\Token\CompositeToken;
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Tests\Doubles;


class SubTokenReaderTest extends TestCase
{
    public function testToken_ReturnsCompositeWithSubToken()
    {
        $reader   = $this->reader('foo');
        $expected = new CompositeToken(
            new ValueToken('namespace', 'foo'),
            new ValueToken('namespace.ext', 'FOO')
        );
        $this->assertEquals($expected, $reader->token('namespace'));
    }

    public function testForInvalidMainToken_Token_ReturnsNull()
    {
        $this->assertNull($this->reader('foo', false)->token());
    }

    public function testValue_ReturnsMainReaderValue()
    {
        $this->assertSame('main value', $this->reader('main value')->value());
    }

    private function reader(string $mainValue, bool $valid = true): SubTokenReader
    {
        $transform = fn(string $value) => strtoupper($value);
        return new SubTokenReader(new Doubles\MockedValueReader($mainValue, $valid), 'ext', $transform);
    }
}
