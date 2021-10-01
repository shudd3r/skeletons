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
    public function testReplacementMethod_ReturnsDefinedReplacement()
    {
        $replacements = new Replacements(['predefined' => $predefined = new FakeReplacement(new FakeRuntimeEnv())]);
        $replacements->add('added', $added = new FakeReplacement(new FakeRuntimeEnv()));

        $this->assertNull($replacements->replacement('undefined'));
        $this->assertSame($predefined, $replacements->replacement('predefined'));
        $this->assertSame($added, $replacements->replacement('added'));
    }

    public function testOverwritingDefinedReplacement_ThrowsException()
    {
        $replacements = new Replacements();
        $replacements->add('defined', new FakeReplacement(new FakeRuntimeEnv()));

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $replacements->add('defined', new FakeReplacement(new FakeRuntimeEnv()));
    }
}
