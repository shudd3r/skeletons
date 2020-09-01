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
use Shudd3r\PackageFiles\Tests\Doubles;


class ValueReadersTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = new Doubles\FakeValueReader(new Doubles\FakeSource('foo'));
        $this->assertInstanceOf(Token\Reader::class, $reader);
        $this->assertInstanceOf(Token\Reader\Source::class, $reader);
        $this->assertInstanceOf(Token\Reader\ValueReader::class, $reader);
    }

    public function testValue_ReturnsValueFromGivenSource()
    {
        $source = new Doubles\FakeSource('some value');
        $reader = new Doubles\FakeValueReader($source);

        $this->assertSame('some value', $reader->value());
    }

    public function testToken_ReturnsTokenFromGivenSource()
    {
        $source = new Doubles\FakeSource('foo');
        $reader = new Doubles\FakeValueReader($source);

        $this->assertEquals(new Doubles\FakeToken('foo'), $reader->token());
    }

    public function testSourceValueIsCached_Value_ReadsSourceOnce()
    {
        $source = new Doubles\FakeSource('some value');
        $reader = new Doubles\FakeValueReader($source);

        $this->assertSame(0, $source->reads);
        $reader->value();
        $this->assertSame(1, $source->reads);
        $reader->value();
        $this->assertSame(1, $source->reads);
    }

    public function testSourceValueIsCached_Token_ReadsSourceOnce()
    {
        $source = new Doubles\FakeSource('some value');
        $reader = new Doubles\FakeValueReader($source);

        $this->assertSame(0, $source->reads);
        $reader->token();
        $this->assertSame(1, $source->reads);
        $reader->token();
        $this->assertSame(1, $source->reads);
    }

    /**
     * @dataProvider subtypes
     *
     * @param string $readerClass
     * @param string $sourceValue
     * @param string $tokenClass
     */
    public function testSubtypes(string $readerClass, string $sourceValue, string $tokenClass)
    {
        $reader = new $readerClass(new Doubles\FakeSource($sourceValue));
        $this->assertInstanceOf($tokenClass, $reader->token());
    }

    public function subtypes(): array
    {
        return [
            [Token\Reader\PackageReader::class, 'package/name', Token\Package::class],
            [Token\Reader\RepositoryReader::class, 'repo/name', Token\Repository::class],
            [Token\Reader\NamespaceReader::class, 'Some\\Namespace', Token\MainNamespace::class],
            [Token\Reader\DescriptionReader::class, 'Package description', Token\Description::class]
        ];
    }
}
