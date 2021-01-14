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


class SrcNamespaceTest extends TestCase
{
    public function testInstantiation()
    {
        $reader = $this->reader('Some\\Namespace');
        $this->assertInstanceOf(Reader\ValueReader::class, $reader);
    }

    public function testReader_TokenMethod_ReturnsCorrectToken()
    {
        $expected = new Token\CompositeToken(
            new Token\ValueToken('namespace', 'Some\\Namespace'),
            new Token\ValueToken('namespace.esc', 'Some\\\\Namespace')
        );
        $this->assertEquals($expected, $this->reader('Some\\Namespace')->token('namespace'));
    }

    private function reader(?string $source): Reader\SrcNamespace
    {
        $source = isset($source) ? new Doubles\FakeSource($source) : null;
        return new Reader\SrcNamespace(new Doubles\FakeValidator(), $source);
    }
}
