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
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\ReplacementReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class ReplacementsTest extends TestCase
{
    public function testValueOfReplacement_ReturnsDefinedReplacementValue()
    {
        $env          = new Doubles\FakeRuntimeEnv();
        $replacements = new Replacements([], [
            'foo' => $this->replacement($env, 'Foo value'),
            'bar' => $this->replacement($env, 'Bar value')
        ]);

        $this->assertSame('', $replacements->valueOf('undefined'));
        $this->assertSame('Foo value', $replacements->valueOf('foo'));
        $this->assertSame('Bar value', $replacements->valueOf('bar'));
    }

    public function testFallbackFromReplacements_ReturnsReplacementValue()
    {
        $env          = new Doubles\FakeRuntimeEnv();
        $replacements = new Replacements([], [
            'first'  => $this->replacement($env, null, 'second'),
            'second' => $this->replacement($env, null, 'third'),
            'third'  => $this->replacement($env, 'third value')
        ]);

        $this->assertSame('third value', $replacements->valueOf('first'));
    }

    public function testCircularFallbackFromReplacements_ReturnsEmptyString()
    {
        $env          = new Doubles\FakeRuntimeEnv();
        $replacements = new Replacements([
            'first'  => $this->replacement($env, null, 'second'),
            'second' => $this->replacement($env, null, 'third'),
            'third'  => $this->replacement($env, null, 'first')
        ]);

        $this->assertSame('', $replacements->valueOf('first', []));
    }

    private function replacement(Doubles\FakeRuntimeEnv $env, ?string $value, string $fallback = null): ReplacementReader
    {
        return new ReplacementReader($env, new Doubles\FakeReplacement($value, null, null, $fallback));
    }
}
