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
use Shudd3r\Skeletons\Replacements\Reader\DataReader;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class DataReaderTest extends TestCase
{
    public function testCommandArgumentMethod_ReturnsNull()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = new DataReader($env, new InputArgs(['script', 'command', '-i', 'fooArg=foo value']));

        $this->assertNull($reader->commandArgument('fooArg'));
    }

    public function testInputValueMethod_ReturnsNullWithoutPromptDisplay()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $this->assertNull($reader->inputValue('Enter foo'));
        $this->assertEmpty($env->output()->messagesSent());
    }

    public function testSendMessageMethod_hasNoEffect()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $reader->sendMessage('Hello world!');
        $this->assertEmpty($env->output()->messagesSent());
    }

    private function reader(Doubles\FakeRuntimeEnv $env): DataReader
    {
        return new DataReader($env, new InputArgs(['script', 'update', '-i', 'fooArg=foo value']));
    }
}
