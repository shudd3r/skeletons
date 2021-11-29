<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Rework\Replacements\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Rework\Replacements\Replacement\PackageDescription;
use Shudd3r\Skeletons\Tests\Doubles\Rework\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token;


class PackageDescriptionTest extends TestCase
{
    public function testWithoutDataToResolveValue_TokenMethod_ReturnsNull()
    {
        $replacement = new PackageDescription('bar');

        $source = Source::create();
        $this->assertNull($replacement->token('foo', $source));
    }

    public function testWithFallbackValue_TokenValueIsResolvedFromThatValue()
    {
        $replacement = new PackageDescription('bar');

        $source = Source::create()->withFallbackTokenValue('bar', 'bar value');
        $this->assertToken('bar value package', $replacement->token('foo', $source));
    }

    public function testWithDescriptionInComposerJsonFile_TokenValueIsResolvedWithComposerData()
    {
        $replacement = new PackageDescription('bar');

        $source = Source::create()->withFallbackTokenValue('bar', 'bar value')
                                  ->withComposerData(['description' => 'Package description']);
        $this->assertToken('Package description', $replacement->token('foo', $source));
    }

    public function testForTokenValueResolvedToEmptyString_TokenMethod_ReturnsNull()
    {
        $replacement = new PackageDescription('bar');

        $source = Source::create()->withFallbackTokenValue('bar', 'bar value')
                                  ->withComposerData(['description' => '']);
        $this->assertNull($replacement->token('foo', $source));

        $source = Source::create()->withFallbackTokenValue('bar', '');
        $this->assertNull($replacement->token('foo', $source));
    }

    private function assertToken(string $value, Token $token): void
    {
        $this->assertEquals(new Token\BasicToken('foo', $value), $token);
    }
}
