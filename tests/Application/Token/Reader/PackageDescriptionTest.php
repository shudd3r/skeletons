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
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('This is my package...');
        $this->assertInstanceOf(Reader\ValueReader::class, $reader);
    }

    public function testReader_TokenMethod_ReturnsCorrectToken()
    {
        $expected = new Token\ValueToken('desc', 'This is my package...');
        $this->assertEquals($expected, $this->reader('This is my package...')->token('desc'));
    }

    public function testReaderValueValidation()
    {
        $reader = $this->reader('valid value', true);
        $this->assertSame('valid value', $reader->value());
        $this->assertInstanceOf(Token::class, $reader->token());

        $reader = $this->reader('invalid value', false);
        $this->assertSame('invalid value', $reader->value());
        $this->assertNull($reader->token());
    }

    private function reader(?string $source, bool $valid = true): Reader\PackageDescription
    {
        $source = isset($source) ? new Doubles\FakeSource($source) : null;
        return new Reader\PackageDescription(new Doubles\FakeValidator($valid), $source);
    }
}
