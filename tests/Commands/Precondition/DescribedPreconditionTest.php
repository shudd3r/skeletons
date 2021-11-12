<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Commands\Precondition;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Commands\Precondition;
use Shudd3r\Skeletons\Tests\Doubles;


class DescribedPreconditionTest extends TestCase
{
    private static Doubles\MockedTerminal $output;

    public static function setUpBeforeClass(): void
    {
        self::$output = new Doubles\MockedTerminal();
    }

    public function testForFulfilledPrecondition_DisplaysOKStatus()
    {
        $described = $this->described(true, 'Checking foo');
        $this->assertTrue($described->isFulfilled());
        $this->assertMessageLine('- Checking foo... OK');
    }

    public function testForFailedPrecondition_DisplaysFAILStatus()
    {
        $described = $this->described(false, 'Checking bar');
        $this->assertFalse($described->isFulfilled());
        $this->assertMessageLine('- Checking bar... FAIL');
    }

    public function testWithoutStatus_DisplaysDescriptionOnly()
    {
        $described = $this->described(true, 'Checking foo', []);
        $this->assertTrue($described->isFulfilled());
        $this->assertMessageLine('- Checking foo');

        $described = $this->described(false, 'Checking bar', []);
        $this->assertFalse($described->isFulfilled());
        $this->assertMessageLine('- Checking bar');
    }

    public function testWithoutFailStatus_DisplaysOKStatus_AndNoNewLineOnFAIL()
    {
        $described = $this->described(true, 'Checking foo', ['DONE']);
        $this->assertTrue($described->isFulfilled());
        $this->assertMessageLine('- Checking foo... DONE');

        $described = $this->described(false, 'Checking foo', ['DONE']);
        $this->assertFalse($described->isFulfilled());
        $this->assertMessageLine('- Checking foo...', false);
    }

    private function assertMessageLine(string $message, bool $newLineEnding = true): void
    {
        $output = implode('', self::$output->messagesSent());
        $this->assertSame($message . ($newLineEnding ? PHP_EOL : ''), $output);
    }

    private function described(bool $isFulfilled, string $description, array $status = null): Precondition
    {
        $precondition = new Doubles\FakePrecondition($isFulfilled);
        return new Precondition\DescribedPrecondition($precondition, self::$output, $description, $status);
    }
}
