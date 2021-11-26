<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


abstract class ReaderTests extends TestCase
{
    /** <placeholder, <<fallback>, <option>, <prompt>> */
    protected const REPLACEMENT_PARAMS = [
        'foo' => ['bar', 'optFoo', 'give Foo'],
        'bar' => ['foo', 'optBar', null],
        'baz' => [null, 'optBaz', 'give Baz']
    ];

    protected const REPLACEMENT_DEFAULTS = [
        'foo' => 'foo (default)',
        'bar' => 'bar (default)',
        'baz' => 'baz (default)'
    ];

    protected const META_DATA = [
        'foo' => 'foo (meta)',
        'bar' => 'bar (meta)',
        'baz' => 'baz (meta)'
    ];

    protected static Doubles\FakeRuntimeEnv $env;

    protected function assertTokenValues(array $expected, array $tokens): void
    {
        $tokenFromValue = fn ($name, $value) => $value !== null ? new Token\BasicToken($name, $value) : null;
        $tokenList      = array_map($tokenFromValue, array_keys($expected), $expected);
        $expectedTokens = array_combine(array_keys($expected), $tokenList);

        $getValue       = fn (?Token $token) => $token ? $token->value() : null;
        $tokenValues    = array_combine(array_keys($tokens), array_map($getValue, $tokens));

        $this->assertEquals($expectedTokens, $tokens);
        $this->assertSame($expected, $tokenValues);
    }

    abstract protected function reader(array $inputs, array $options, array $metaData = []): Reader;

    abstract protected function defaults(array $override = []): array;

    protected function replacements(array $removeDefaults = []): Replacements
    {
        $override     = fn(string $name, string $default) => in_array($name, $removeDefaults) ? null : $default;
        $defaults     = array_map($override, array_keys(self::REPLACEMENT_DEFAULTS), self::REPLACEMENT_DEFAULTS);
        $create       = fn (?string $default, array $params) => new Doubles\FakeReplacement($default, ...$params);
        $replacements = array_map($create, $defaults, self::REPLACEMENT_PARAMS);

        return new Replacements(array_combine(array_keys(self::REPLACEMENT_DEFAULTS), $replacements));
    }

    protected function env(array $inputs = [], array $metaData = []): Doubles\FakeRuntimeEnv
    {
        self::$env = new Doubles\FakeRuntimeEnv();
        array_walk($inputs, fn ($input) => self::$env->input()->addInput($input));
        if ($metaData) { self::$env->metaData()->save($metaData); }

        return self::$env;
    }

    protected function args(array $args): InputArgs
    {
        return new InputArgs(array_merge(['script', 'init'], $args));
    }
}
