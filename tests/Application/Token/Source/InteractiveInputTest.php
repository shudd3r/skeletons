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
use Shudd3r\PackageFiles\Application\Token\Source\PredefinedValue;
use Shudd3r\PackageFiles\Tests\Doubles;


class InteractiveInputTest extends TestCase
{
    public function testEmptyInputReturnsDefaultValue()
    {
        $terminal = new Doubles\MockedTerminal();

        $source = $this->source($terminal, 'default');
        $this->assertSourceValue('default', $source);

        $source = $this->source($terminal, '');
        $this->assertSourceValue('', $source);
    }

    public function testNotEmptyInputReturnsInputString()
    {
        $terminal = new Doubles\MockedTerminal();
        $terminal->addInput('input string');

        $source = $this->source($terminal, 'default');
        $this->assertSourceValue('input string', $source);
    }

    public function testRepeatedInput()
    {
        $terminal = new Doubles\MockedTerminal();
        $terminal->addInput('foo');
        $terminal->addInput('bar');

        $source = $this->source($terminal, 'baz (default)');
        $this->assertSourceValue('foo', $source);
        $this->assertSourceValue('bar', $source);
        $this->assertSourceValue('baz (default)', $source);
        $this->assertCount(3, $terminal->messagesSent());
    }

    public function testTerminalDisplaysCorrectPrompt()
    {
        $terminal = new Doubles\MockedTerminal();
        $source   = $this->source($terminal, '');

        $this->assertEmpty($terminal->messagesSent());

        $source->value();
        $this->assertSame(['Input value:'], $terminal->messagesSent());

        $terminal = new Doubles\MockedTerminal();
        $source   = $this->source($terminal, 'default value');

        $source->value();
        $this->assertSame(['Input value [default: `default value`]:'], $terminal->messagesSent());
    }

    private function assertSourceValue(string $value, InteractiveInput $source): void
    {
        $this->assertSame($value, $source->value());
    }

    private function source(Doubles\MockedTerminal $terminal, string $default): InteractiveInput
    {
        return new InteractiveInput('Input value', $terminal, new PredefinedValue($default));
    }
}
