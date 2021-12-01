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
        $reader = new InputReader($env, new InputArgs(['command', 'update', 'fooArg=foo command line value']));

        $this->assertSame('foo command line value', $reader->commandArgument('fooArg'));
        $this->assertSame('', $reader->commandArgument('notArg'));
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
        $this->assertStringContainsString('Invalid value', array_pop($messages));
    }
}