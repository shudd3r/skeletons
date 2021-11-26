<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Rework;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Rework\Replacements;
use Shudd3r\Skeletons\Tests\Doubles\Rework\FakeReplacement;


class ReplacementsTest extends TestCase
{
    public function testPlaceholdersMethod_ReturnsDefinedPlaceholderNames()
    {
        $replacements = new Replacements([
            'foo' => new FakeReplacement(),
            'bar' => new FakeReplacement()
        ]);
        $this->assertSame(['foo', 'bar'], $replacements->placeholders());
    }

    public function testReplacementMethod_ReturnsDefinedReplacement()
    {
        $replacements = new Replacements($replacementArray = [
            'foo' => new FakeReplacement()
        ]);
        $this->assertSame($replacementArray['foo'], $replacements->replacement('foo'));
        $this->assertNull($replacements->replacement('bar'));
    }

    public function testInfoMethod_ReturnsFilteredArrayOfDescriptions()
    {
        $replacements = new Replacements([
            'foo' => new FakeReplacement('This is foo'),
            'bar' => new FakeReplacement(''),
            'baz' => new FakeReplacement('This is baz')
        ]);

        $expected = ['foo' => 'This is foo', 'baz' => 'This is baz'];
        $this->assertSame($expected, $replacements->info());
    }
}
