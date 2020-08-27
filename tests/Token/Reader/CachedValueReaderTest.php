<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\CachedValueReader;
use Shudd3r\PackageFiles\Tests\Doubles\FakeValueReader;


class CachedValueReaderTest extends TestCase
{
    public function testWrappedReaderValueMemoization()
    {
        $cached = $this->cachedReader($wrapped, 'value');

        $this->assertSame(0, $wrapped->reads);
        $this->assertSame('value', $cached->value());
        $this->assertSame(1, $wrapped->reads);
        $this->assertSame('value', $cached->value());
        $this->assertSame(1, $wrapped->reads);
    }

    public function testToken_ReturnsSameInstance()
    {
        $cached = $this->cachedReader($wrapped, 'value');

        $firstToken = $cached->token();
        $this->assertSame($wrapped->created, $firstToken);
        $this->assertSame(1, $wrapped->reads);

        $secondToken = $cached->token();
        $this->assertSame($wrapped->created, $secondToken);
        $this->assertSame($firstToken, $secondToken);
        $this->assertSame(1, $wrapped->reads);
    }

    public function testCreateToken_ReturnsNewInstance()
    {
        $cached = $this->cachedReader($wrapped, 'foo');

        $token = $cached->createToken('bar');
        $this->assertSame(0, $wrapped->reads);
        $this->assertNotSame($token, $cached->createToken('bar'));
    }

    private function cachedReader(FakeValueReader &$mock = null, string $value = 'foo'): CachedValueReader
    {
        return new CachedValueReader($mock ??= new FakeValueReader($value));
    }
}
