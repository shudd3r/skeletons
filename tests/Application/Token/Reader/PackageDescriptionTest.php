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
        $expected = new Token\ValueToken('{description.text}', 'This is my package...');
        $this->assertEquals($expected, $this->reader('This is my package...')->token());
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testReaderValueValidation(string $invalid, string $valid)
    {
        $reader = $this->reader($invalid);
        $this->assertSame($invalid, $reader->value());
        $this->assertNull($reader->token());

        $reader = $this->reader($valid);
        $this->assertSame($valid, $reader->value());
        $this->assertInstanceOf(Token::class, $reader->token());
    }

    public function valueExamples()
    {
        return [['', 'package description']];
    }

    private function reader(?string $source): Reader\PackageDescription
    {
        $source = isset($source) ? new Doubles\FakeSource($source) : null;
        return new Reader\PackageDescription($source);
    }
}
