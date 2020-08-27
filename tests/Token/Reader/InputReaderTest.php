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
use Shudd3r\PackageFiles\Token\Reader\InputReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class InputReaderTest extends TestCase
{
    public function testForGivenInput_Value_ReturnsInput()
    {
        $reader = $this->inputReader($this->terminal('input value'));
        $this->assertSame('input value', $reader->value());
    }

    public function testWithoutInput_Value_ReturnsStringFromWrappedReader()
    {
        $reader = $this->inputReader($this->terminal());
        $this->assertSame('wrapped', $reader->value());
    }

    public function testForGivenInput_Token_ReturnsTokenWithInput()
    {
        $reader = $this->inputReader($this->terminal('input value'));
        $this->assertEquals(new Doubles\FakeToken('input value'), $reader->token());
    }

    public function testWithoutInput_Token_ReturnsTokenFromWrappedReader()
    {
        $reader = $this->inputReader($this->terminal());
        $this->assertEquals(new Doubles\FakeToken('wrapped'), $reader->token());
    }

    public function testForGivenValue_CreateToken_ReturnsTokenFromWrappedReader()
    {
        $reader = $this->inputReader($this->terminal('input value'), $wrapped);
        $this->assertEquals($wrapped->createToken('given value'), $reader->createToken('given value'));
    }

    public function testWrappedReaderPromptIsDisplayedWithDefaultValue()
    {
        $terminal = $this->terminal();
        $reader   = $this->inputReader($terminal, $wrapped);

        $reader->token();
        $prompt = 'Prompt [default: ' . $wrapped->value() . ']:';
        $this->assertSame($terminal->messagesSent[0], $prompt);
    }

    private function inputReader(Doubles\MockedTerminal $terminal, Doubles\FakeValueReader &$mock = null): InputReader
    {
        return new InputReader('Prompt', $terminal, $mock = new Doubles\FakeValueReader('wrapped'));
    }

    private function terminal(string $input = null): Doubles\MockedTerminal
    {
        return new Doubles\MockedTerminal([$input]);
    }
}
