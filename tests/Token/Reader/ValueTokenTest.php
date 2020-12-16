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
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Token\Parser;
use Shudd3r\PackageFiles\Tests\Doubles;


class ValueTokenTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader();
        $this->assertInstanceOf(Reader::class, $reader);
        $this->assertInstanceOf(Parser::class, $reader);
    }

    public function testReader_WithSourceMethod_CreatesNewInstanceWithChangedSource()
    {
        $baseReader = $this->reader('base value');
        $this->assertSame('base value', $baseReader->value());

        $newReader = $baseReader->withSource(new Doubles\FakeSource('new value'));

        $this->assertNotEquals($baseReader, $newReader);
        $this->assertSame('new value', $newReader->value());
    }

    public function testReaderWithSourceWithoutValue_ValueMethod_ReturnsParsedValue()
    {
        $reader = $this->reader(null);
        $this->assertSame($reader->parsedValue(), $reader->value());
    }

    public function testReaderWithSourceProvidedValue_ValueMethod_ReturnsSourceValue()
    {
        $reader = $this->reader('source value');
        $this->assertNotEquals($reader->parsedValue(), $reader->value());
        $this->assertSame('source value', $reader->value());
    }

    public function testReaderWithSourceWithoutValue_TokenMethod_ReturnsTokenUsingParsedValue()
    {
        $this->assertEquals(new Doubles\FakeToken('parsed value'), $this->reader(null)->token());
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
        $this->assertSame('parsed value', $reader->value());
        $this->assertNull($reader->token());
    }

    public function testTokenValueIsCached()
    {
        $reader = new Doubles\MockedValueToken();

        $this->assertSame(0, $reader->source->reads);
        $reader->value();
        $this->assertSame(1, $reader->source->reads);
        $reader->value();
        $this->assertSame(1, $reader->source->reads);
    }

    private function reader(?string $sourceValue = 'foo', bool $valid = true): Reader\ValueToken
    {
        return new Doubles\MockedValueToken($sourceValue, $valid);
    }
}
