<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Source\InteractiveInput;
use Shudd3r\PackageFiles\Tests\Doubles;


class InteractiveInputTest extends TestCase
{
    public function testEmptyInputReturnsDefaultValue()
    {
        $source = $this->source($terminal, 'default');
        $terminal->inputStrings = [''];
        $this->assertSourceValue('default', $source);

        $source = $this->source($terminal, '');
        $terminal->inputStrings = [''];
        $this->assertSourceValue('', $source);
    }

    public function testNotEmptyInputReturnsInputString()
    {
        $source = $this->source($terminal, 'default');
        $terminal->inputStrings = ['input string'];
        $this->assertSourceValue('input string', $source);
    }

    public function testRepeatedInput()
    {
        $source = $this->source($terminal, 'baz (default)');
        $terminal->inputStrings = ['foo', 'bar'];

        $this->assertSourceValue('foo', $source);
        $this->assertSourceValue('bar', $source);
        $this->assertSourceValue('baz (default)', $source);
        $this->assertCount(3, $terminal->messagesSent);
    }

    public function testTerminalDisplaysCorrectPrompt()
    {
        $source = $this->source($terminal, '');
        $this->assertSame([], $terminal->messagesSent);
        $source->value(new Doubles\FakeValidator());
        $this->assertSame(['Input value:'], $terminal->messagesSent);

        $source = $this->source($terminal, 'default value');
        $source->value(new Doubles\FakeValidator());
        $this->assertSame(['Input value [default: `default value`]:'], $terminal->messagesSent);
    }

    private function assertSourceValue(string $value, InteractiveInput $source): void
    {
        $this->assertSame($value, $source->value(new Doubles\FakeValidator()));
    }

    private function source(?Doubles\MockedTerminal &$terminal, string $default = 'default'): InteractiveInput
    {
        $terminal = new Doubles\MockedTerminal();
        return new InteractiveInput('Input value', $terminal, new Doubles\FakeSource($default));
    }
}
