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
use Shudd3r\PackageFiles\Tests\Doubles\FakeReplacement;
use Shudd3r\PackageFiles\Tests\Doubles\FakeRuntimeEnv;
use Shudd3r\PackageFiles\Application\Exception;


class ReplacementsTest extends TestCase
{
    public function testValueOfReplacement_ReturnsDefinedReplacementValue()
    {
        $predefined = new FakeReplacement(new FakeRuntimeEnv(), null, 'predefinedTokenValue');
        $added      = new FakeReplacement(new FakeRuntimeEnv(), null, 'addedTokenValue');

        $replacements = new Replacements(['predefined' => $predefined]);
        $replacements->add('added', $added);

        $this->assertSame('', $replacements->valueOf('undefined', []));
        $this->assertSame('predefinedTokenValue', $replacements->valueOf('predefined', []));
        $this->assertSame('addedTokenValue', $replacements->valueOf('added', []));
    }

    public function testOverwritingDefinedReplacement_ThrowsException()
    {
        $replacements = new Replacements();
        $replacements->add('defined', new FakeReplacement(new FakeRuntimeEnv()));

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $replacements->add('defined', new FakeReplacement(new FakeRuntimeEnv()));
    }

    public function testFallbackFromReplacements_ReturnsReplacementValue()
    {
        $env          = new FakeRuntimeEnv();
        $replacements = $env->replacements();

        $replacements->add('first', new FakeReplacement($env, 'second', null));
        $replacements->add('second', new FakeReplacement($env, 'third', null));
        $replacements->add('third', new FakeReplacement($env, null, 'third value'));

        $this->assertSame('third value', $replacements->valueOf('first', []));
    }

    public function testCircularFallbackFromReplacements_ReturnsEmptyString()
    {
        $env          = new FakeRuntimeEnv();
        $replacements = $env->replacements();

        $replacements->add('first', new FakeReplacement($env, 'second', null));
        $replacements->add('second', new FakeReplacement($env, 'third', null));
        $replacements->add('third', new FakeReplacement($env, 'first', null));

        $this->assertSame('', $replacements->valueOf('first', []));
    }
}
