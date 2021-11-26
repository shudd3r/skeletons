<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Processors\Processor;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Processors\Processor;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Tests\Doubles;


class DescribedProcessorTest extends TestCase
{
    private static Doubles\MockedTerminal $output;
    private static Token\BasicToken       $token;

    public static function setUpBeforeClass(): void
    {
        self::$output = new Doubles\MockedTerminal();
        self::$token  = new Token\BasicToken('placeholder', 'value');
    }

    public function testForSuccessfulProcess_DisplaysOKStatus()
    {
        $described = $this->described(true, 'Checking foo');
        $this->assertTrue($described->process(self::$token));
        $this->assertMessageLine('    Checking foo... OK');
    }

    public function testForFailedProcess_DisplaysFAILStatus()
    {
        $described = $this->described(false, 'Checking bar');
        $this->assertFalse($described->process(self::$token));
        $this->assertMessageLine('    Checking bar... FAIL');
    }

    public function testWithoutStatus_DisplaysDescriptionOnly()
    {
        $described = $this->described(true, 'Checking foo', false);
        $this->assertTrue($described->process(self::$token));
        $this->assertMessageLine('    Checking foo');

        $described = $this->described(false, 'Checking bar', false);
        $this->assertFalse($described->process(self::$token));
        $this->assertMessageLine('    Checking bar');
    }

    private function assertMessageLine(string $message): void
    {
        $output = implode('', self::$output->messagesSent());
        $this->assertSame($message . PHP_EOL, $output);
    }

    private function described(bool $isSuccessful, string $message, bool $status = true): Processor
    {
        $processor = new Doubles\MockedProcessor($isSuccessful);
        return new Processor\DescribedProcessor($processor, self::$output, $message, $status);
    }
}
