<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Replacements;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Replacements;
use Shudd3r\PackageFiles\Application\Replacements\Reader;
use Shudd3r\PackageFiles\Application\Replacements\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


abstract class ReaderTests extends TestCase
{
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

    public function testInvalidToken_ReturnsNull() {
        $reader = $this->reader([], [], ['baz']);
        $this->assertNull($reader->token());
        $this->assertSame($this->defaults(['baz' => null]), $reader->tokenValues());
    }

    protected function assertTokenValues(Reader $reader, array $expected): void
    {
        $createToken   = fn($name, $value) => new Token\ValueToken($name, $value);
        $expectedToken = new Token\CompositeToken(...array_map($createToken, array_keys($expected), $expected));
        $this->assertEquals($expectedToken, $reader->token());
        $this->assertSame($expected, $reader->tokenValues());
    }

    abstract protected function reader(array $inputs, array $options, array $removeDefaults = []): Reader;

    abstract protected function defaults(array $override = []): array;

    protected function replacements(array $removeDefaults): Replacements
    {
        $override     = fn(string $name, string $default) => in_array($name, $removeDefaults) ? null : $default;
        $defaults     = array_map($override, array_keys(self::REPLACEMENT_DEFAULTS), self::REPLACEMENT_DEFAULTS);
        $create       = fn (?string $default, array $params) => new Doubles\FakeReplacement($default, ...$params);
        $replacements = array_map($create, $defaults, self::REPLACEMENT_PARAMS);

        return new Replacements(array_combine(array_keys(self::REPLACEMENT_DEFAULTS), $replacements));
    }

    protected function env(array $inputs = [], array $metaData = []): Doubles\FakeRuntimeEnv
    {
        $env = new Doubles\FakeRuntimeEnv();
        array_walk($inputs, fn ($input) => $env->input()->addInput($input));
        if ($metaData) { $env->metaData()->save($metaData); }

        return $env;
    }
}
