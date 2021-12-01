<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\ApplicationTests;

use Shudd3r\Skeletons\Tests\ApplicationTests;
use Shudd3r\Skeletons\Application;
use Shudd3r\Skeletons\Tests\Doubles;


class HelpTest extends ApplicationTests
{
    public function testWithoutDefinedReplacementOptions_DisplaysBaseMessage()
    {
        $app = $this->dummyApp();
        $this->assertSame(0, $app->run($this->args('help')));

        $expectedStart = <<<HELP
            Usage from package root directory:
            vendor/bin/script-name <command> [<options>] [<argument>=<value> ...]
            
            HELP;

        $expectedEnd = <<<HELP
            No available <arguments> for placeholder <values>
            
            HELP;

        $this->assertMessage($expectedStart, $expectedEnd);
    }

    public function testWithDefinedReplacementOptions_DisplaysArgsInfo()
    {
        $app = $this->dummyApp();
        $app->replacement('foo.test')
            ->add(Doubles\Rework\FakeReplacement::create()->withInputArg('foo')->withDescription('Option Foo...'));
        $app->replacement('bar.test')
            ->add(Doubles\Rework\FakeReplacement::create()->withDescription('Option Bar'));
        $app->replacement('baz.test')
            ->add(Doubles\Rework\FakeReplacement::create()->withInputArg('baz')->withDescription('Option Baz...'));

        $this->assertSame(0, $app->run($this->args('help')));

        $expectedStart = <<<HELP
            Usage from package root directory:
            vendor/bin/script-name <command> [<options>] [<argument>=<value> ...]
            
            HELP;

        $expectedEnd = <<<HELP
            Available <arguments> for placeholder <values>:
              foo         Option Foo...
              baz         Option Baz...
            
            HELP;

        $this->assertMessage($expectedStart, $expectedEnd);
    }

    private function assertMessage(string $expectedStart, string $expectedEnd): void
    {
        $messages = self::$terminal->messagesSent();
        $helpMessage = str_replace(PHP_EOL, "\n", $messages[1]);

        $this->assertStringStartsWith($expectedStart, $helpMessage);
        $this->assertStringEndsWith($expectedEnd, $helpMessage);
    }

    private function dummyApp(): Application
    {
        return new Application(self::$files->directory(), self::$files->directory(), self::$terminal->reset());
    }
}
