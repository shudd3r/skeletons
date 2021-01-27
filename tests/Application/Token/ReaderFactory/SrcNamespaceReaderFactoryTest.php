<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\ReaderFactory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class SrcNamespaceReaderFactoryTest extends TestCase
{
    public function testTokenFactoryMethod_CreatesCorrectToken()
    {
        $subToken = new Token\ValueToken('namespace.esc', 'Some\\\\Namespace');
        $expected = new Token\CompositeValueToken('namespace', 'Some\\Namespace', $subToken);

        $this->assertEquals($expected, $this->replacement()->token('namespace', 'Some\Namespace'));
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethod_ValidatesValue(string $invalid, string $valid)
    {
        $replacement = $this->replacement();
        $this->assertInstanceOf(Token::class, $replacement->token('foo', $valid));
        $this->assertNull($replacement->token('foo', $invalid));
    }

    public function valueExamples()
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function replacement(): Token\ReaderFactory\SrcNamespaceReaderFactory
    {
        $env     = new Doubles\FakeRuntimeEnv();
        $package = new Token\ReaderFactory\PackageNameReaderFactory($env, []);
        return new Token\ReaderFactory\SrcNamespaceReaderFactory($env, [], $package);
    }
}
