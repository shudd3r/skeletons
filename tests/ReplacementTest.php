<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\ValueToken;


class ReplacementTest extends TestCase
{
    public function testForInvalidValue_TokenMethods_ReturnNull()
    {
        $replacement = new Doubles\FakeReplacement(new Doubles\FakeRuntimeEnv(), null, 'invalid value', false);
        $this->assertNull($replacement->initialToken('foo', []));
        $this->assertNull($replacement->updateToken('foo', []));
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
        $env     = new Doubles\FakeRuntimeEnv();

        $env->metaDataFile()->write('{"foo": "metaData"}');
        if ($option) {
            $options['option'] = 'option value';
        }

        if ($input) {
            $options['i'] = true;
            $env->input()->addInput('input value');
            $env->input()->addInput('input value');
        }

        $replacement = new Doubles\FakeReplacement($env);
        $this->assertEquals(new ValueToken('foo', $init), $replacement->initialToken('foo', $options));
        $this->assertEquals(new ValueToken('foo', 'metaData'), $replacement->validationToken('foo'));
        $this->assertEquals(new ValueToken('foo', $update), $replacement->updateToken('foo', $options));
    }

    public function testResolvingFallbackValue()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->replacements()->add('fallback', new Doubles\FakeReplacement($env, null, 'fallback value'));

        $replacement = new Doubles\FakeReplacement($env, 'fallback', null);
        $this->assertEquals(new ValueToken('foo', 'fallback value'), $replacement->initialToken('foo', []));
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
