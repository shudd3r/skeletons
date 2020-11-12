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
use Exception;


class PackageReaderTest extends TestCase
{
    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidReaderValue_ThrowsException(string $invalid, string $valid)
    {
        $reader = $this->reader($valid);
        $this->assertInstanceOf(Token::class, $reader->token());
        $reader = $this->reader($invalid);
        $this->expectException(Exception::class);
        $reader->token();
    }

    public function valueExamples()
    {
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    protected function reader(string $value): Token\Reader
    {
        return new Token\Reader\PackageReader(new Doubles\FakeSource($value));
    }
}
