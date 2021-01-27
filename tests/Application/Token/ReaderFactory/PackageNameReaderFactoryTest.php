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

class PackageNameReaderFactoryTest extends TestCase
{
    public function testTokenFactoryMethod_ReturnsCorrectToken()
    {
        $subToken = new Token\ValueToken('package.name.title', 'Source/Package');
        $expected = new Token\CompositeValueToken('package.name', 'source/package', $subToken);
        $this->assertEquals($expected, $this->replacement()->token('package.name', 'source/package'));
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
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    private function replacement(): Token\ReaderFactory\PackageNameReaderFactory
    {
        return new Token\ReaderFactory\PackageNameReaderFactory(new Doubles\FakeRuntimeEnv(), []);
    }
}
