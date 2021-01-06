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
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Validator;
use Shudd3r\PackageFiles\Tests\Doubles;


class ValueTokenTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader();
        $this->assertInstanceOf(Reader::class, $reader);
        $this->assertInstanceOf(Validator::class, $reader);
    }

    public function testReader_WithSourceMethod_CreatesNewInstanceWithChangedSource()
    {
        $baseReader = $this->reader('base value');
        $this->assertSame('base value', $baseReader->value());

        $newReader = $baseReader->withSource(new Doubles\FakeSource('new value'));

        $this->assertNotEquals($baseReader, $newReader);
        $this->assertSame('new value', $newReader->value());
    }

    public function testReaderWithoutSource_ValueMethod_ReturnsEmptyString()
    {
        $reader = $this->reader(null);
        $this->assertSame('', $reader->value());
    }

    public function testReaderWithSourceProvidedValue_ValueMethod_ReturnsSourceValue()
    {
        $reader = $this->reader('source value');
        $this->assertSame('source value', $reader->value());
    }

    public function testReaderWithoutSource_TokenMethod_ReturnsTokenUsingEmptyString()
    {
        $this->assertEquals(new Doubles\FakeToken(''), $this->reader(null)->token());
    }

    public function testReaderWithSourceProvidedValue_TokenMethod_ReturnsTokenUsingSourceValue()
    {
        $this->assertEquals(new Doubles\FakeToken('source value'), $this->reader('source value')->token());
    }

    public function testInvalidTokenValue()
    {
        $reader = $this->reader('source value', false);
        $this->assertSame('source value', $reader->value());
        $this->assertNull($reader->token());

        $reader = $this->reader(null, false);
        $this->assertSame('', $reader->value());
        $this->assertNull($reader->token());
    }

    public function testTokenValueIsCached()
    {
        $reader = new Doubles\MockedValueReader();

        $this->assertSame(0, $reader->source->reads);
        $reader->value();
        $this->assertSame(1, $reader->source->reads);
        $reader->value();
        $this->assertSame(1, $reader->source->reads);
    }

    private function reader(?string $sourceValue = 'foo', bool $valid = true): Reader\ValueReader
    {
        return new Doubles\MockedValueReader($sourceValue, $valid);
    }
}
