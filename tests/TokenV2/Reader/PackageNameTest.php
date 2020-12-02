<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\TokenV2\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\TokenV2\Reader;
use Shudd3r\PackageFiles\TokenV2\Parser;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageNameTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('some/name', 'composer/name');
        $this->assertInstanceOf(Reader::class, $reader);
        $this->assertInstanceOf(Parser::class, $reader);
    }

    public function testReaderWithEmptyComposerName_ParsedValueMethod_ResolvesNameFromDirectoryStructure()
    {
        $reader = $this->reader(null, null, 'foo/directory');
        $this->assertSame('foo/directory', $reader->parsedValue());
    }

    public function testReaderWithComposerName_ParsedValueMethod_ResolvesNameFromComposer()
    {
        $reader = $this->reader(null, 'composer/package', 'foo/directory');
        $this->assertSame('composer/package', $reader->parsedValue());
    }

    public function testReaderWithSourceWithoutValue_ValueMethod_ReturnsParsedValue()
    {
        $reader = $this->reader(null, 'composer/package');
        $this->assertSame($reader->parsedValue(), $reader->value());
    }

    public function testReaderWithSourceProvidedValue_ValueMethod_ReturnsSourceValue()
    {
        $reader = $this->reader('source/package', 'composer/package');
        $this->assertNotEquals($reader->parsedValue(), $reader->value());
        $this->assertSame('source/package', $reader->value());
    }

    public function testReaderWithSourceWithoutValue_TokenMethod_ReturnsTokenUsingParsedValue()
    {
        $reader = $this->reader(null, 'composer/package');
        $this->assertToken('composer/package', $reader);
    }

    public function testReaderWithSourceProvidedValue_TokenMethod_ReturnsTokenUsingSourceValue()
    {
        $reader = $this->reader('source/package-name', 'composer/package');
        $this->assertToken('source/package-name', $reader);
    }

    public function testReaderCachesValue()
    {
        $source = new Doubles\FakeSourceV2();
        $reader = new Reader\PackageName($this->composer(), new Doubles\FakeDirectory(), $source);

        $this->assertSame(0, $source->reads);
        $reader->value();
        $this->assertSame(1, $source->reads);
        $reader->value();
        $this->assertSame(1, $source->reads);
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testReaderValueValidation(string $invalid, string $valid)
    {
        $reader = $this->reader($invalid, 'composer/package');
        $this->assertSame($invalid, $reader->value());
        $this->assertNull($reader->token());

        $reader = $this->reader($valid, 'composer/package');
        $this->assertSame($valid, $reader->value());
        $this->assertToken($valid, $reader);
    }

    public function valueExamples()
    {
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    private function assertToken(string $value, Reader $reader): void
    {
        $expected = new Token\CompositeToken(
            new Token\ValueToken('{package.name}', $value),
            new Token\ValueToken('{package.title}', $this->titleName($value))
        );
        $this->assertEquals($expected, $reader->token());
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }

    private function reader(?string $source, ?string $composer, ?string $directory = 'foo/bar'): Reader\PackageName
    {
        return new Reader\PackageName(
            $this->composer($composer ?? ''),
            new Doubles\FakeDirectory($directory),
            new Doubles\FakeSourceV2($source)
        );
    }

    private function composer(string $packageName = ''): ComposerJsonData
    {
        $contents = json_encode($packageName ? ['name' => $packageName] : []);
        $composer = new Doubles\MockedFile($contents);
        return new ComposerJsonData($composer);
    }
}
