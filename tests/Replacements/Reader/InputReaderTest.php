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
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class InputReaderTest extends TestCase
{
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

    public function testForNonInteractiveCommand_InputStringMethod_ReturnsNull()
    {
        $reader = new InputReader(new Doubles\FakeRuntimeEnv(), new InputArgs(['script', 'command']));
        $this->assertNull($reader->inputString('Give foo:'));
    }

    public function testWithoutValidation_InputStringMethod_ReturnsFirstInputValue()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = new InputReader($env, new InputArgs(['script', 'update']));

        $env->input()->addInput('invalid', 'valid value');
        $this->assertSame('invalid', $reader->inputString('Give value:'));
        $this->assertSame(['Give value:'], $env->output()->messagesSent());
    }

    public function testInputStringMethod_ReturnsValidInputGivenWithinRetryLimit()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = new InputReader($env, new InputArgs(['script', 'update']));

        $input   = $env->input();
        $isValid = fn (string $value) => $value !== 'invalid';

        $input->addInput('input string');
        $this->assertSame('input string', $reader->inputString('Give value for foo:', $isValid));
        $this->assertSame(['Give value for foo:'], $input->messagesSent());

        $input->reset()->addInput('invalid', 'invalid', 'valid value');
        $this->assertSame('valid value', $reader->inputString('Give value for foo:', $isValid));

        $input->reset()->addInput('invalid', 'invalid', 'invalid', 'valid value');
        $this->assertSame('invalid', $reader->inputString('Give value for foo:', $isValid));
        $messages = $input->messagesSent();
        $this->assertStringStartsWith('Invalid value', trim(array_pop($messages)));
    }

    public function testInputStringWithoutRetryLimit_ReturnsFirstValidValue()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = new InputReader($env, new InputArgs(['script', 'update']));

        $isValid = fn (string $value) => $value !== 'invalid';

        $env->input()->addInput('invalid', 'invalid', 'invalid', 'invalid', 'invalid', 'invalid', 'valid value');
        $this->assertSame('valid value', $reader->inputString('Give value:', $isValid, 0));
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
