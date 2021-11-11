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
        $package   = new Doubles\FakeDirectory();
        $templates = new Doubles\FakeDirectory();
        $app = new Application($package, $templates, self::$terminal->reset());

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
        $package   = new Doubles\FakeDirectory();
        $templates = new Doubles\FakeDirectory();
        $app = new Application($package, $templates, self::$terminal->reset());

        $app->replacement('foo.test')->add(new Doubles\FakeReplacement(null, null, 'foo', 'Option Foo'));
        $app->replacement('bar.test')->add(new Doubles\FakeReplacement(null, null, null, 'Option Bar'));
        $app->replacement('baz.test')->add(new Doubles\FakeReplacement(null, null, 'baz', 'Option Baz'));

        $this->assertSame(0, $app->run($this->args('help')));

        $expectedStart = <<<HELP
            Usage from package root directory:
            vendor/bin/script-name <command> [<options>] [<argument>=<value> ...]
            
            HELP;

        $expectedEnd = <<<HELP
            Available <arguments> for placeholder <values>:
                foo       Option Foo [format: anything]
                baz       Option Baz [format: anything]
            
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
}
