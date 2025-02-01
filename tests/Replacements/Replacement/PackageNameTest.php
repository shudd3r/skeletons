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
use Shudd3r\Skeletons\Replacements\Replacement\PackageName;
use Shudd3r\Skeletons\Tests\Doubles\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token;


class PackageNameTest extends TestCase
{
    private static PackageName $replacement;

    public static function setUpBeforeClass(): void
    {
        self::$replacement = new PackageName();
    }

    public function test_without_data_to_resolve_value_token_method_returns_null(): void
    {
        $this->assertNull(self::$replacement->token('foo', Source::create()));
    }

    public function test_value_is_resolved_from_package_directory_path(): void
    {
        $source = Source::create()->withPackagePath('/path/to/directory/package');
        $this->assertToken('Directory/Package', $source);
    }

    public function test_value_is_resolved_with_package_name_in_composer_json_file(): void
    {
        $source = Source::create()->withPackagePath('/path/to/directory/package')
                                  ->withComposerData(['name' => 'composer/package']);
        $this->assertToken('Composer/Package', $source);
    }

    /** @dataProvider validReplacementValues */
    public function test_default_values_are_capitalized(string $original, string $capitalized): void
    {
        $this->assertToken($capitalized, Source::create()->withPackagePath('/path/to/' . $original));
        $this->assertToken($capitalized, Source::create()->withComposerData(['name' => $original]));
    }

    /** @dataProvider validReplacementValues */
    public function test_direct_source_value_is_not_capitalized(string $baseValue): void
    {
        $this->assertToken($baseValue, Source::create(['foo' => $baseValue]));
    }

    public static function validReplacementValues(): array
    {
        return [
            'no changes needed'     => ['PackageAuthor/NoChange', 'PackageAuthor/NoChange'],
            'segments only'         => ['package/name', 'Package/Name'],
            'segments & separators' => ['package.vendor/name_test-value', 'Package.Vendor/Name_Test-Value'],
            'starts with number'    => ['1package000/namE', '1package000/NamE']
        ];
    }

    /** @dataProvider invalidReplacementValues */
    public function test_for_invalid_source_value_token_method_returns_null(string $invalidValue): void
    {
        $source = Source::create(['foo' => $invalidValue]);
        $this->assertNull(self::$replacement->token('foo', $source));
    }

    public static function invalidReplacementValues(): array
    {
        return [['-Packa-ge1/na.me'], ['1Package000_/na_Me'], ['package/na-me-']];
    }

    private function assertToken(string $packageName, Source $source): void
    {
        $token    = self::$replacement->token('foo', $source);
        $expected = Token\CompositeToken::withValueToken(
            new Token\BasicToken('foo', $packageName),
            new Token\BasicToken('foo.composer', strtolower($packageName))
        );

        $this->assertEquals($expected, $token);
    }
}
