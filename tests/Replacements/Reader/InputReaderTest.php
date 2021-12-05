<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Reader\InputReader;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class InputReaderTest extends TestCase
{
    public function testForInvalidTokenInInteractiveMode_TokensMethod_ReturnsAtFirstNullValue()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('foo-value'),
            'bar' => new Doubles\FakeReplacement('invalid'),
            'baz' => new Doubles\FakeReplacement('baz-value'),
        ]);

        $expected = [
            'foo' => new BasicToken('foo', 'foo-value'),
            'bar' => null
        ];

        $this->assertEquals($expected, $reader->tokens($replacements));

        $messages = $env->output()->messagesSent();
        $this->assertSame('Aborting...', trim($messages[0]));
    }

    public function testCommandArgument_ReturnsInputArgSourceValue()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $args   = new InputArgs(['command', 'update', 'fooArg=foo command line value', 'barArg=', 'bazArg']);
        $reader = new InputReader($env, $args);

        $this->assertSame('foo command line value', $reader->commandArgument('fooArg'));
        $this->assertSame('', $reader->commandArgument('barArg'));
        $this->assertSame('', $reader->commandArgument('bazArg'));
        $this->assertNull($reader->commandArgument('notArg'));
    }

    public function testForNonInteractiveMode_InputValueMethod_ReturnsNull()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, false);

        $this->assertNull($reader->inputValue('Enter foo'));
        $this->assertEmpty($env->output()->messagesSent());
    }

    public function testForInteractiveMode_InputValueMethod_ReturnsPromptedUserInput()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $env->input()->addInput('input value');

        $this->assertSame('input value',  $reader->inputValue('Enter foo'));
        $this->assertSame(['  > Enter foo:'], $env->output()->messagesSent());
    }

    public function testSendMessageMethod_SendsIndentedMessageToOutput()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $message = <<<'MSG'
            Hello world!
            This is second line of the message
            MSG;
        $expected = <<<'MSG'
                Hello world!
                This is second line of the message
            
            MSG;

        $reader->sendMessage($message);
        $this->assertSame([str_replace("\n", PHP_EOL, $expected)], $env->output()->messagesSent());
    }

    private function reader(Doubles\FakeRuntimeEnv $env, bool $isInteractive = true): InputReader
    {
        return new InputReader($env, new InputArgs(['script', 'update', $isInteractive ? '-i' : 'fooArg=arg value']));
    }
}
