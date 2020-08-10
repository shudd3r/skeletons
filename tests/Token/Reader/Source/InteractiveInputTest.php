<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Tests\Doubles;


class InteractiveInputTest extends TestCase
{
    public function testInputValue()
    {
        $terminal = new Doubles\MockedTerminal();
        $source   = new Source\InteractiveInput('Prompt text', $terminal);

        $terminal->inputStrings[] = 'some value';
        $this->assertSame('some value', $source->value());
        $this->assertSame(['Prompt text:'], $terminal->messagesSent);
    }

    public function testDefaultValue()
    {
        $terminal = new Doubles\MockedTerminal();
        $default  = new Source\CallbackSource(fn() => 'default value');
        $source   = new Source\InteractiveInput('Prompt text', $terminal, $default);

        $terminal->inputStrings[] = '';
        $this->assertSame('default value', $source->value());
        $this->assertSame(['Prompt text [default: default value]:'], $terminal->messagesSent);

        $terminal->inputStrings[] = 'input value';
        $this->assertSame('input value', $source->value());
    }
}
