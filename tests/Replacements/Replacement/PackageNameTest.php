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

    public function testWithoutEnoughDataToResolveValue_TokenMethod_ReturnsNull()
    {
        $this->assertNull(self::$replacement->token('foo', Source::create()));
    }

    public function testForPackageDirectoryWithParentDirectory_TokenValueIsResolvedFromPackageDirectoryPath()
    {
        $source = Source::create()->withPackagePath('/path/to/directory/package');
        $this->assertToken('Directory/Package', self::$replacement->token('foo', $source));
    }

    public function testWithPackageNameInComposerJsonFile_TokenValueIsResolvedWithComposerJsonData()
    {
        $source = Source::create()->withPackagePath('/path/to/directory/package')
                                  ->withComposerData(['name' => 'composer/package']);
        $this->assertToken('Composer/Package', self::$replacement->token('foo', $source));
    }

    /**
     * @dataProvider validReplacementValues
     * @param string $original
     * @param string $capitalized
     */
    public function testValuesResolvedForDefault_AreCapitalized(string $original, string $capitalized)
    {
        $fromDirectory = Source::create()->withPackagePath('/path/to/' . $original);
        $this->assertToken($capitalized, self::$replacement->token('foo', $fromDirectory));

        $fromComposer = Source::create()->withComposerData(['name' => $original]);
        $this->assertToken($capitalized, self::$replacement->token('foo', $fromComposer));
    }

    /**
     * @dataProvider validReplacementValues
     *
     * @param string $baseValue
     */
    public function testDirectSourceValue_IsNotCapitalized(string $baseValue)
    {
        $source = Source::create(['foo' => $baseValue]);
        $this->assertToken($baseValue, self::$replacement->token('foo', $source));
    }

    /**
     * @dataProvider invalidReplacementValues
     * @param string $invalidValue
     */
    public function testForInvalidSourceValue_TokenMethod_ReturnsNull(string $invalidValue)
    {
        $source = Source::create(['foo' => $invalidValue]);
        $this->assertNull(self::$replacement->token('foo', $source));
    }

    public function validReplacementValues(): array
    {
        return [
            'no changes needed'     => ['PackageAuthor/NoChange', 'PackageAuthor/NoChange'],
            'segments only'         => ['package/name', 'Package/Name'],
            'segments & separators' => ['package.vendor/name_test-value', 'Package.Vendor/Name_Test-Value'],
            'starts with number'    => ['1package000/namE', '1package000/NamE']
        ];
    }

    public function invalidReplacementValues(): array
    {
        return [['-Packa-ge1/na.me'], ['1Package000_/na_Me'], ['package/na-me-']];
    }

    private function assertToken(string $packageName, Token $token): void
    {
        $expected = Token\CompositeToken::withValueToken(
            new Token\BasicToken('foo', $packageName),
            new Token\BasicToken('foo.composer', strtolower($packageName))
        );

        $this->assertEquals($expected, $token);
    }
}
