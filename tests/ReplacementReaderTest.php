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
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\ReplacementReader;
use Shudd3r\PackageFiles\Application\Token\ValueToken;
use Shudd3r\PackageFiles\Tests\Doubles\FakeReplacement;


class ReplacementReaderTest extends TestCase
{
    public function testForInvalidValue_TokenMethods_ReturnNull()
    {
        $replacement = new ReplacementReader(new FakeReplacement(null), new Doubles\FakeRuntimeEnv(), []);

        $this->assertNull($replacement->initialToken('foo', new Replacements([])));
        $this->assertNull($replacement->updateToken('foo'));
        $this->assertNull($replacement->validationToken('foo'));
    }

    /**
     * @dataProvider inputOptions
     * @param bool $option
     * @param bool $input
     * @param string $initValue
     * @param string $updateValue
     */
    public function testResolvingInputValues(bool $option, bool $input, string $initValue, string $updateValue)
    {
        $options = [];
        $env     = new Doubles\FakeRuntimeEnv();

        $env->metaDataFile()->write('{"foo": "meta data"}');
        if ($option) {
            $options['option'] = 'option value';
        }

        if ($input) {
            $options['i'] = true;
            $env->input()->addInput('input value');
            $env->input()->addInput('input value');
        }

        $replacement = new ReplacementReader(new FakeReplacement('default value'), $env, $options);
        $this->assertEquals(new ValueToken('foo', $initValue), $replacement->initialToken('foo', new Replacements($options)));
        $this->assertEquals(new ValueToken('foo', 'meta data'), $replacement->validationToken('foo'));
        $this->assertEquals(new ValueToken('foo', $updateValue), $replacement->updateToken('foo'));
    }

    public function testInitialToken_IsCached()
    {
        $replacement = new ReplacementReader(new FakeReplacement('default value'), new Doubles\FakeRuntimeEnv(), []);

        $initialToken = $replacement->initialToken('token.name', new Replacements([]));
        $this->assertSame($initialToken, $replacement->initialToken('another.name', new Replacements([])));
    }

    public function inputOptions(): array
    {
        return [
            'no user values' => [false, false, 'default value', 'meta data'],
            'option only'    => [true, false, 'option value', 'option value'],
            'input only'     => [false, true, 'input value', 'input value'],
            'input & option' => [true, true, 'input value', 'input value']
        ];
    }
}
