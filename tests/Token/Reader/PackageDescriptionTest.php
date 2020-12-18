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
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('This is my package...');
        $this->assertInstanceOf(Reader\ValueReader::class, $reader);
    }

    public function testReaderWithEmptyComposerDescription_ParsedValueMethod_ResolvesDescriptionFromPackageName()
    {
        $reader = $this->reader('anything', false);
        $this->assertSame('package/name package', $reader->parsedValue());
    }

    public function testReaderWithComposerDescription_ParsedValueMethod_ResolvesDescriptionFromComposerFile()
    {
        $reader = $this->reader(null);
        $this->assertSame('composer package description', $reader->parsedValue());
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

    private function reader(?string $source, bool $composer = true): Reader\PackageDescription
    {
        $contents = json_encode($composer ? ['description' => 'composer package description'] : []);
        $composer = new Reader\Data\ComposerJsonData(new Doubles\MockedFile($contents));
        $package  = new Doubles\FakePackageName();

        return isset($source)
            ? new Reader\PackageDescription($composer, $package, new Doubles\FakeSource($source))
            : new Reader\PackageDescription($composer, $package);
    }
}
