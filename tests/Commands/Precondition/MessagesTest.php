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
use Shudd3r\Skeletons\Commands\Precondition\Messages;
use Shudd3r\Skeletons\Tests\Doubles;


class MessagesTest extends TestCase
{
    private static Doubles\MockedTerminal $output;

    public static function setUpBeforeClass(): void
    {
        self::$output = new Doubles\MockedTerminal();
    }

    /** @dataProvider expectedOutputs */
    public function testMessageWithDefaultInstanceValues(array $params, string $success, string $failure, int $error)
    {
        $message = new Messages(self::$output->reset(), ...$params);

        $message->describeProcedure();
        $message->sendResult(true);
        $this->assertOutput($success);

        $message->describeProcedure();
        $message->sendResult(false);
        $this->assertOutput($failure, $error);
    }

    public function testMessageWithoutStatusDisplay()
    {
        $message = new Messages(self::$output->reset(), 'Testing OK status:', []);

        $message->describeProcedure();
        $message->sendResult(true);
        $this->assertOutput('- Testing OK status:' . PHP_EOL);

        $message->describeProcedure();
        $message->sendResult(false);
        $this->assertOutput('- Testing OK status:' . PHP_EOL, 2);
    }

    public function expectedOutputs(): array
    {
        return [
            'Default instance' => [['Testing defaults'],
                '- Testing defaults... OK' . PHP_EOL,
                '- Testing defaults... FAIL' . PHP_EOL, 2
            ],
            'OK only' => [['Testing OK status', ['DONE']],
                '- Testing OK status... DONE' . PHP_EOL,
                '- Testing OK status...', 2
            ],
            'No status' => [['Testing NO status', []],
                '- Testing NO status' . PHP_EOL,
                '- Testing NO status' . PHP_EOL, 2
            ],
            'Error code with default status messages' => [['Testing defaults', null, 128],
                '- Testing defaults... OK' . PHP_EOL,
                '- Testing defaults... FAIL' . PHP_EOL, 128
            ]
        ];
    }

    private function assertOutput(string $expected, int $exitCode = 0): void
    {
        $output = implode('', self::$output->messagesSent());
        $this->assertSame($expected, $output);
        $this->assertSame($exitCode, self::$output->exitCode());
    }
}
