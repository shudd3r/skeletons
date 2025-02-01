<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Tests\Doubles\FakeReplacement;


class ReplacementsTest extends TestCase
{
    public function test_placeholders_method_returns_defined_placeholder_names(): void
    {
        $replacements = new Replacements([
            'foo' => new FakeReplacement(),
            'bar' => new FakeReplacement()
        ]);
        $this->assertSame(['foo', 'bar'], $replacements->placeholders());
    }

    public function test_replacement_method_returns_defined_Replacement(): void
    {
        $replacements = new Replacements($replacementArray = [
            'foo' => new FakeReplacement()
        ]);
        $this->assertSame($replacementArray['foo'], $replacements->replacement('foo'));
        $this->assertNull($replacements->replacement('bar'));
    }

    public function test_info_method_returns_filtered_array_of_Replacement_descriptions(): void
    {
        $replacement = new FakeReplacement();
        $replacements = new Replacements($replacementArray = [
            'foo' => $replacement->withInputArg('fooArg')->withDescription('This is foo'),
            'bar' => $replacement->withDescription('No argument - no description'),
            'baz' => $replacement->withInputArg('bazArg')->withDescription("This is baz")
        ]);

        $expected = [
            $replacementArray['foo']->description('foo'),
            $replacementArray['baz']->description('baz')
        ];
        $this->assertSame($expected, $replacements->info());
    }
}
