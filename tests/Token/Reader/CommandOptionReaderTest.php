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
use Shudd3r\PackageFiles\Token\Reader\CommandOptionReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class CommandOptionReaderTest extends TestCase
{
    public function testValue_ReturnsOptionValue()
    {
        $reader = $this->optionReader($wrapped, 'option value');
        $this->assertNotSame($wrapped->value(), $reader->value());
        $this->assertSame('option value', $reader->value());
        $this->assertEquals(new Doubles\FakeToken('option value'), $reader->token());
    }

    public function testCreateToken_ReturnsTokenCreatedByWrapper()
    {
        $reader = $this->optionReader($wrapped);

        $token = $reader->createToken('foo');
        $this->assertEquals(new Doubles\FakeToken('foo'), $token);
        $this->assertSame($token, $wrapped->created);
    }

    private function optionReader(Doubles\FakeValueReader &$mock = null, string $option = ''): CommandOptionReader
    {
        $mock = new Doubles\FakeValueReader('wrapped');
        return new CommandOptionReader($option ?? 'option', $mock);
    }
}
