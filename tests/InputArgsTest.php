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
            'command only'  => [true, ['init']],
            'no args'       => [true, ['init', '-opt']],
            'short update'  => [true, ['update', '-i', 'foo=bar']],
            'no option'     => [false, ['init', 'foo=bar']],
            'other short'   => [false, ['init', '-a', 'foo=bar']],
            'other long'    => [false, ['init', '--long-args', '--it', 'foo=bar']],
            'wrong command' => [false, ['sync', '-i', 'foo=bar']],
            'grouped short' => [true, ['init', '-string']],
            'correct long'  => [true, ['init', '--long-args', '--interactive']],
            'long & short'  => [true, ['init', '--long-args', '--interactive', '-shi']],
        ];
    }

    /**
     * @dataProvider localFilesArgs
     * @param bool   $expected
     * @param array  $args
     */
    public function testLocalFilesOption(bool $expected, array $args)
    {
        $args = new InputArgs(array_merge(['script'], $args));
        $this->assertSame($expected, $args->includeLocalFiles());
    }

    public function localFilesArgs(): array
    {
        return [
            'no args'       => [false, ['init']],
            'short'         => [true, ['init', '-l']],
            'missing short' => [false, ['init', '-a']],
            'grouped short' => [true, ['init', '-glued']],
            'missing long'  => [false, ['init', '--long-args', '--loc']],
            'correct long'  => [true, ['init', '--long-args', '--local']],
            'long & short'  => [true, ['init', '--long-args', '--local', '-il']],
        ];
    }

    public function testArgumentValues()
    {
        $argv = ['script', 'init', 'none', 'foo=value', '--notArg=foo', 'empty=', 'withSpaces=foo bar baz'];
        $args = new InputArgs($argv);
        $this->assertSame('', $args->valueOf('undefined'));
        $this->assertSame('', $args->valueOf('notArg'));
        $this->assertSame('', $args->valueOf('--notArg'));
        $this->assertSame('', $args->valueOf('none'));
        $this->assertSame('', $args->valueOf('empty'));
        $this->assertSame('value', $args->valueOf('foo'));
        $this->assertSame('foo bar baz', $args->valueOf('withSpaces'));
    }
}
