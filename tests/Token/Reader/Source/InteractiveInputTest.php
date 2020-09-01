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
use Shudd3r\PackageFiles\Token\Reader\Source\InteractiveInput;
use Shudd3r\PackageFiles\Tests\Doubles\MockedTerminal;
use Shudd3r\PackageFiles\Tests\Doubles\FakeSource;


class InteractiveInputTest extends TestCase
{
    public function testValue_ReturnsInputString()
    {
        $source = new InteractiveInput('Some property', new MockedTerminal(['some value', '']));
        $this->assertSame('some value', $source->value());
        $this->assertSame('', $source->value());
    }

    public function testGivenFallbackSource_ValueForEmptyInput_ReturnsFallbackValue()
    {
        $fallback = new FakeSource('fallback value');
        $source   = new InteractiveInput('Some property', new MockedTerminal(['some value', '']), $fallback);
        $this->assertSame('some value', $source->value());
        $this->assertSame('fallback value', $source->value());
    }

    public function testPromptIsSentToInputMethod()
    {
        $input  = new MockedTerminal();
        $prompt = 'Input prompt';

        (new InteractiveInput($prompt, $input))->value();
        $this->assertSame($prompt . ':', $input->messagesSent[0]);

        (new InteractiveInput($prompt, $input, new FakeSource('default value')))->value();
        $this->assertSame($prompt . ' [default: default value]:', $input->messagesSent[1]);
    }
}
