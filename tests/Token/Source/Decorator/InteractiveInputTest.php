<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Source\Decorator;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Source\Decorator\InteractiveInput;
use Shudd3r\PackageFiles\Tests\Doubles;


class InteractiveInputTest extends TestCase
{
    public function testCreate_IsDelegatedToWrappedSource()
    {
        $wrapped = new Doubles\FakeSource('');
        $source  = new InteractiveInput('Prompt', new Doubles\MockedTerminal(['foo']), $wrapped);
        $this->assertSame($source->create('test'), $wrapped->created);
    }

    public function testValue_ReturnsInputString()
    {
        $source = new InteractiveInput('Prompt', new Doubles\MockedTerminal(['input']), new Doubles\FakeSource('foo'));
        $this->assertSame('input', $source->value());
    }

    public function testForEmptyInput_Value_ReturnsWrappedValue()
    {
        $wrapped = new Doubles\FakeSource('bar');
        $source  = new InteractiveInput('Prompt', new Doubles\MockedTerminal(['foo', '']), $wrapped);
        $this->assertSame('foo', $source->value());
        $this->assertSame('bar', $source->value());
    }

    public function testPromptIsSentToInputMethod()
    {
        $input  = new Doubles\MockedTerminal();
        $prompt = 'Input prompt';
        $source = new InteractiveInput($prompt, $input, new Doubles\FakeSource(''));

        $source->value();
        $this->assertSame($prompt . ':', $input->messagesSent[0]);

        $source = new InteractiveInput($prompt, $input, new Doubles\FakeSource('foo'));

        $source->value();
        $this->assertSame($prompt . ' [default: foo]:', $input->messagesSent[1]);
    }
}
