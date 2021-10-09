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
use Shudd3r\PackageFiles\Application\Exception;
use Shudd3r\PackageFiles\Tests\Doubles;


class ReplacementsTest extends TestCase
{
    public function testValueOfReplacement_ReturnsDefinedReplacementValue()
    {
        $env        = new Doubles\FakeRuntimeEnv();
        $predefined = $this->replacement($env, 'predefinedTokenValue');
        $added      = $this->replacement($env, 'addedTokenValue');

        $replacements = new Replacements(['predefined' => $predefined]);
        $replacements->add('added', $added);

        $this->assertSame('', $replacements->valueOf('undefined', []));
        $this->assertSame('predefinedTokenValue', $replacements->valueOf('predefined', []));
        $this->assertSame('addedTokenValue', $replacements->valueOf('added', []));
    }

    public function testOverwritingDefinedReplacement_ThrowsException()
    {
        $env          = new Doubles\FakeRuntimeEnv();
        $replacements = $env->replacements();

        $replacements->add('defined', $this->replacement($env, 'foo'));

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $replacements->add('defined', $this->replacement($env, 'bar'));
    }

    public function testFallbackFromReplacements_ReturnsReplacementValue()
    {
        $env          = new Doubles\FakeRuntimeEnv();
        $replacements = $env->replacements();

        $replacements->add('first', $this->replacement($env, null, 'second'));
        $replacements->add('second', $this->replacement($env, null, 'third'));
        $replacements->add('third', $this->replacement($env, 'third value'));

        $this->assertSame('third value', $replacements->valueOf('first', []));
    }

    public function testCircularFallbackFromReplacements_ReturnsEmptyString()
    {
        $env          = new Doubles\FakeRuntimeEnv();
        $replacements = $env->replacements();

        $replacements->add('first', $this->replacement($env, null, 'second'));
        $replacements->add('second', $this->replacement($env, null, 'third'));
        $replacements->add('third', $this->replacement($env, null, 'first'));

        $this->assertSame('', $replacements->valueOf('first', []));
    }

    private function replacement(Doubles\FakeRuntimeEnv $env, ?string $value, string $fallback = null): ReplacementReader
    {
        return new ReplacementReader($env, new Doubles\FakeReplacement($value, null, null, $fallback));
    }
}
