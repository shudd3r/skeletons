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


class PackageNameTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('some/name');
        $this->assertInstanceOf(Reader\ValueReader::class, $reader);
    }

    public function testReaderWithEmptyComposerName_ParsedValueMethod_ResolvesNameFromDirectoryStructure()
    {
        $reader = $this->reader(null, false);
        $this->assertSame('root/path', $reader->parsedValue());
    }

    public function testReaderWithComposerName_ParsedValueMethod_ResolvesNameFromComposer()
    {
        $reader = $this->reader(null);
        $this->assertSame('composer/package', $reader->parsedValue());
    }

    public function testReader_TokenMethod_ReturnsCorrectToken()
    {
        $expected = new Token\CompositeToken(
            new Token\ValueToken('{package.name}', 'source/package'),
            new Token\ValueToken('{package.title}', 'Source/Package')
        );
        $this->assertEquals($expected, $this->reader('source/package')->token());
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
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    private function reader(?string $source, bool $composer = true): Reader\PackageName
    {
        $composer  = $this->composer($composer ? 'composer/package' : '');
        $directory = new Doubles\FakeDirectory('root/path');

        return isset($source)
            ? new Reader\PackageName($composer, $directory, new Doubles\FakeSource($source))
            : new Reader\PackageName($composer, $directory);
    }

    private function composer(string $packageName = ''): Reader\Data\ComposerJsonData
    {
        $contents = json_encode($packageName ? ['name' => $packageName] : []);
        $composer = new Doubles\MockedFile($contents);
        return new Reader\Data\ComposerJsonData($composer);
    }
}