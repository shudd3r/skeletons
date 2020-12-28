<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Environment;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Environment as App;


class TerminalTest extends TestCase
{
    public function testInstantiation()
    {
        $terminal = $this->terminal($input, $output, $error);
        $this->assertInstanceOf(App\Terminal::class, $terminal);
        $this->assertInstanceOf(App\Input::class, $terminal);
        $this->assertInstanceOf(App\Output::class, $terminal);
    }

    public function testRender_SendsMessagesToOutputStream()
    {
        $terminal = $this->terminal($input, $output, $error);
        $messages = ['foo message', 'bar message', 'baz message'];

        array_walk($messages, fn (string $message) => $terminal->send($message));

        rewind($output);
        $this->assertSame(implode('', $messages), fgets($output));
    }

    public function testRenderWithErrorCode_SendsMessagesErrorStream()
    {
        $terminal = $this->terminal($input, $output, $error);
        $messages = ['foo message', 'bar message', 'baz message'];

        array_walk($messages, fn (string $message) => $terminal->send($message, 1));

        rewind($error);
        $this->assertSame(implode('', $messages), fgets($error));
    }

    public function testRenderWithErrorCode_CollectsBinarySumOfErrorCodes()
    {
        $terminal = $this->terminal($input, $output, $error);
        $messages = [32 => 'foo message', 8 => 'bar message', 1 => 'baz message', 9 => 'already covered'];

        array_walk($messages, fn (string $message, int $code) => $terminal->send($message, $code));

        $this->assertSame(32 | 8 | 1 | 9, $terminal->exitCode());
    }

    public function testInput_ReadsLineFromInputStream()
    {
        $terminal = $this->terminal($input, $output, $error);
        $messages = ['foo message', 'bar message', 'baz message'];

        fwrite($input, implode("\n", $messages));
        rewind($input);

        foreach ($messages as $message) {
            $this->assertSame($message, $terminal->value());
        }
    }

    private function terminal(&$input, &$output, &$error): App\Terminal
    {
        $input  = fopen('php://memory', 'r+b');
        $output = fopen('php://memory', 'w+b');
        $error  = fopen('php://memory', 'r+b');

        return new App\Terminal($input, $output, $error);
    }
}
