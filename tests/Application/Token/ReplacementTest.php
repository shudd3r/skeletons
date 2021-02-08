<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Tests\Doubles\FakeReplacement;
use Shudd3r\PackageFiles\Tests\Doubles\FakeRuntimeEnv;


class ReplacementTest extends TestCase
{
    public function testForInvalidValue_TokenMethods_ReturnNull()
    {
        $replacement = new FakeReplacement(new FakeRuntimeEnv(), [], false);
        $this->assertNull($replacement->initialToken('foo'));
        $this->assertNull($replacement->updateToken('foo'));
        $this->assertNull($replacement->validationToken('foo'));
    }

    /**
     * @dataProvider inputOptions
     * @param bool $option
     * @param bool $input
     * @param string $init
     * @param string $update
     */
    public function testResolvingInputValues(bool $option, bool $input, string $init, string $update)
    {
        $options = [];
        $env     = new FakeRuntimeEnv();

        $env->metaDataFile()->contents = '{"foo": "metaData"}';
        if ($option) {
            $options['option'] = 'option value';
        }

        if ($input) {
            $options['i'] = true;
            $env->input()->inputStrings = ['input value', 'input value'];
        }

        $replacement = new FakeReplacement($env, $options);

        $this->assertEquals(new ValueToken('foo', $init), $replacement->initialToken('foo'));
        $this->assertEquals(new ValueToken('foo', 'metaData'), $replacement->validationToken('foo'));
        $this->assertEquals(new ValueToken('foo', $update), $replacement->updateToken('foo'));
    }

    public function inputOptions(): array
    {
        return [
            [false, false, 'default value', 'metaData'],
            [true, false, 'option value', 'option value'],
            [false, true, 'input value', 'input value'],
            [true, true, 'input value', 'input value']
        ];
    }
}
