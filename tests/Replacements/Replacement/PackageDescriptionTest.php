<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Replacement\PackageDescription;
use Shudd3r\Skeletons\Tests\Doubles\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token;


class PackageDescriptionTest extends TestCase
{
    private static PackageDescription $replacement;

    public static function setUpBeforeClass(): void
    {
        self::$replacement = new PackageDescription('bar');
    }

    public function test_without_data_to_resolve_value_token_method_returns_null(): void
    {
        $this->assertNull(self::$replacement->token('foo', Source::create()));
    }

    public function test_with_fallback_value_Token_value_is_resolved_from_that_value(): void
    {
        $source = Source::create()->withFallbackTokenValue('bar', 'bar value');
        $this->assertToken('bar value package', $source);
    }

    public function test_value_is_resolved_with_description_in_composer_json_file(): void
    {
        $source = Source::create()->withFallbackTokenValue('bar', 'bar value')
                                  ->withComposerData(['description' => 'Package description']);
        $this->assertToken('Package description', $source);
    }

    public function test_for_value_resolved_to_empty_string_token_method_returns_null(): void
    {
        $source = Source::create()->withFallbackTokenValue('bar', 'bar value')
                                  ->withComposerData(['description' => '']);
        $this->assertNull(self::$replacement->token('foo', $source));

        $source = Source::create()->withFallbackTokenValue('bar', '');
        $this->assertNull(self::$replacement->token('foo', $source));
    }

    private function assertToken(string $value, Source $source): void
    {
        $token = self::$replacement->token('foo', $source);
        $this->assertEquals(new Token\BasicToken('foo', $value), $token);
    }
}
