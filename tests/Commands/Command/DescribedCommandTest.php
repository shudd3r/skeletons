<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Commands\Command;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Commands\Command\DescribedCommand;
use Shudd3r\PackageFiles\Tests\Doubles;


class DescribedCommandTest extends TestCase
{
    public function testExecute_DisplaysMessage()
    {
        $output  = new Doubles\MockedTerminal();
        $message = 'Doing something now';
        $command = new DescribedCommand(new Doubles\FakeCommand(), $output, $message);

        $this->assertEmpty($output->messagesSent());

        $command->execute();
        $this->assertSame(['- Doing something now' . PHP_EOL], $output->messagesSent());
    }
}
