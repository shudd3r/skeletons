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
use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Tests\Doubles;


class SrcNamespaceTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('Some\\Namespace');
        $this->assertInstanceOf(Reader\ValueToken::class, $reader);
    }

    public function testReaderWithEmptyComposerNamespace_ParsedValueMethod_ResolvesNamespaceFromPackageName()
    {
        $reader = $this->reader('anything', false);
        $this->assertSame('Package\\Name', $reader->parsedValue());
    }

    public function testReaderWithComposerNamespace_ParsedValueMethod_ResolvesNamespaceFromComposerFile()
    {
        $reader = $this->reader(null);
        $this->assertSame('Composer\\Namespace', $reader->parsedValue());
    }

    public function testReader_TokenMethod_ReturnsCorrectToken()
    {
        $expected = new Token\CompositeToken(
            new Token\ValueToken('{namespace.src}', 'Some\\Namespace'),
            new Token\ValueToken('{namespace.src.esc}', 'Some\\\\Namespace')
        );
        $this->assertEquals($expected, $this->reader('Some\\Namespace')->token());
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
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function reader(?string $source, bool $composer = true): Reader\SrcNamespace
    {
        $contents = json_encode($composer ? ['autoload' => ['psr-4' => ['Composer\\Namespace\\' => 'src/']]] : []);
        $composer = new ComposerJsonData(new Doubles\MockedFile($contents));
        $package  = new Doubles\FakePackageName();

        return isset($source)
            ? new Reader\SrcNamespace($composer, $package, new Doubles\FakeSource($source))
            : new Reader\SrcNamespace($composer, $package);
    }
}
