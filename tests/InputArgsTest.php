<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\InputArgs;


class InputArgsTest extends TestCase
{
    public function testEmptyArrayInstance_ReturnsDefaultValues()
    {
        $args = new InputArgs([]);
        $this->assertSame('skeleton-script', $args->script());
        $this->assertSame('help', $args->command());
    }

    public function testScriptNameOnlyInstance_ReturnsDefaultCommand()
    {
        $args = new InputArgs(['script-only']);
        $this->assertSame('script-only', $args->script());
        $this->assertSame('help', $args->command());
    }

    public function testCommandValue_IsAlwaysSecondArgument()
    {
        $args = new InputArgs(['script-name', '--this-is-command']);
        $this->assertSame('--this-is-command', $args->command());
    }

    /**
     * @dataProvider interactiveArgs
     * @param bool   $expected
     * @param array  $args
     */
    public function testInteractiveOption(bool $expected, array $args)
    {
        $args = new InputArgs(array_merge(['script'], $args));
        $this->assertSame($expected, $args->interactive());
    }

    public function interactiveArgs(): array
    {
        return [
            'no args'       => [false, ['init']],
            'wrong command' => [false, ['sync', '-i']],
            'short init'    => [true, ['init', '-i']],
            'short update'  => [true, ['update', '-i']],
            'missing short' => [false, ['init', '-a']],
            'grouped short' => [true, ['init', '-string']],
            'missing long'  => [false, ['init', '--long-args', '--it']],
            'correct long'  => [true, ['init', '--long-args', '--interactive']],
            'long & short'  => [true, ['init', '--long-args', '--interactive', '-shi']],
        ];
    }

    /**
     * @dataProvider remoteOnlyArgs
     * @param bool   $expected
     * @param array  $args
     */
    public function testRemoteOnlyOption(bool $expected, array $args)
    {
        $args = new InputArgs(array_merge(['script'], $args));
        $this->assertSame($expected, $args->remoteOnly());
    }

    public function remoteOnlyArgs(): array
    {
        return [
            'no args'       => [false, ['init']],
            'short'         => [true, ['init', '-r']],
            'missing short' => [false, ['init', '-a']],
            'grouped short' => [true, ['init', '-string']],
            'missing long'  => [false, ['init', '--long-args', '--rem']],
            'correct long'  => [true, ['init', '--long-args', '--remote']],
            'long & short'  => [true, ['init', '--long-args', '--remote', '-ir']],
        ];
    }

    public function testArgumentValues()
    {
        $argv = ['script', 'command', 'none', 'value=foo', '--notArg=foo', 'empty=', 'withSpace=foo bar baz'];
        $args = new InputArgs($argv);
        $this->assertSame('', $args->valueOf('undefined'));
        $this->assertSame('', $args->valueOf('notArg'));
        $this->assertSame('', $args->valueOf('--notArg'));
        $this->assertSame('', $args->valueOf('none'));
        $this->assertSame('', $args->valueOf('empty'));
        $this->assertSame('foo', $args->valueOf('value'));
        $this->assertSame('foo bar baz', $args->valueOf('withSpace'));
    }
}
