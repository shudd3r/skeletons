<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader\Source\Decorator;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\Source\Decorator\ErrorMessageOutput;
use Shudd3r\PackageFiles\Tests\Doubles;


class ErrorMessageOutputTest extends TestCase
{
    public function testMissingTokenFromSource_SendsErrorMessageToOutput()
    {
        $output = new Doubles\MockedTerminal();
        $source = new ErrorMessageOutput(new Doubles\FakeSource(null), $output, 'MyToken');

        $this->assertNull($source->create('foo'));
        $this->assertSame(['Invalid MyToken value: `foo`'], $output->messagesSent);
    }

    public function testValue_ReturnsValueFromWrappedSource()
    {
        $source = new ErrorMessageOutput(new Doubles\FakeSource('foo'), new Doubles\MockedTerminal(), 'MyToken');
        $this->assertSame('foo', $source->value());
    }
}
