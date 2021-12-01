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
    public function testUserInputMethods_InputSourceValues_ReturnEmptyString()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = new DataReader($env, new InputArgs(['script', 'command', '-i', 'fooArg=foo value']));

        $env->input()->addInput('input string');
        $isValid = fn (string $value) => $value !== 'invalid';

        $this->assertSame('', $reader->inputString('Input prompt:', $isValid));
        $this->assertSame('', $reader->commandArgument('fooArg'));
    }
}
