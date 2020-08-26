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
    public function testForExistingWrappedOption_Value_ReturnsOptionValue()
    {
        $reader = $this->optionReader($wrapped, 'option value');
        $this->assertNotSame($wrapped->value(), $reader->value());
        $this->assertSame('option value', $reader->value());
        $this->assertEquals(new Doubles\FakeToken('option value'), $reader->token());
    }

    public function testForUndefinedWrapperOption_Value_ReturnsWrappedValue()
    {
        $reader = $this->optionReader($wrapped);
        $this->assertSame($wrapped->value(), $reader->value());
        $this->assertSame('wrapped', $reader->value());
        $this->assertEquals(new Doubles\FakeToken('wrapped'), $reader->token());
    }

    public function testConstantPropertiesAreReadFromWrappedReader()
    {
        $reader = $this->optionReader($wrapped);

        $this->assertSame($wrapped->inputPrompt(), $reader->inputPrompt());
        $this->assertSame($wrapped->optionName(), $reader->optionName());
    }

    public function testCreateToken_ReturnsTokenCreatedByWrapper()
    {
        $reader = $this->optionReader($wrapped);

        $token = $reader->createToken('foo');
        $this->assertEquals(new Doubles\FakeToken('foo'), $token);
        $this->assertSame($token, $wrapped->created);
    }

    private function optionReader(Doubles\FakeValueReader &$mock = null, string $option = null): CommandOptionReader
    {
        $mock = new Doubles\FakeValueReader('wrapped');
        return new CommandOptionReader($option ? ['option' => $option] : [], $mock);
    }
}
