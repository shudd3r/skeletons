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

    public function testReader_TokenMethod_ReturnsCorrectToken()
    {
        $expected = new Token\CompositeToken(
            new Token\ValueToken('package.name', 'source/package'),
            new Token\ValueToken('package.name.title', 'Source/Package')
        );
        $this->assertEquals($expected, $this->reader('source/package')->token('package.name'));
    }

    public function testReaderValueValidation()
    {
        $reader = $this->reader('valid/value', true);
        $this->assertSame('valid/value', $reader->value());
        $this->assertInstanceOf(Token::class, $reader->token());

        $reader = $this->reader('invalid/value', false);
        $this->assertSame('invalid/value', $reader->value());
        $this->assertNull($reader->token());
    }

    private function reader(?string $source, bool $valid = true): Reader\PackageName
    {
        $source = isset($source) ? new Doubles\FakeSource($source) : null;
        return new Reader\PackageName(new Doubles\FakeValidator($valid), $source);
    }
}
