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
use Shudd3r\Skeletons\Replacements\Replacement\SrcNamespace;
use Shudd3r\Skeletons\Tests\Doubles\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token;


class SrcNamespaceTest extends TestCase
{
    private static SrcNamespace $replacement;

    public static function setUpBeforeClass(): void
    {
        self::$replacement = new SrcNamespace('bar');
    }

    public function testWithoutEnoughDataToResolveValue_TokenMethod_ReturnsNull()
    {
        $this->assertNull(self::$replacement->token('foo', Source::create()));
    }

    public function testWithCorrectFallbackValue_TokenValueIsResolvedFromFallback()
    {
        $source = Source::create()->withFallbackTokenValue('bar', 'invalid fallback value');
        $this->assertNull(self::$replacement->token('foo', $source));

        $source = Source::create()->withFallbackTokenValue('bar', 'fallback/package-name');
        $this->assertToken('Fallback\\PackageName', $source);
    }

    public function testWithMatchingNamespaceInComposerJsonFile_TokenValueIsResolvedWithComposerData()
    {
        $composer = fn (string $directory) => ['autoload' => ['psr-4' => ['Composer\\Namespace\\' => $directory]]];
        $source   = Source::create()->withFallbackTokenValue('bar', 'fallback/package-name');

        $source = $source->withComposerData($composer('app/'));
        $this->assertToken('Fallback\\PackageName', $source);

        $source = $source->withComposerData($composer('src/'));
        $this->assertToken('Composer\\Namespace', $source);
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testResolvedTokenValue_IsValidated(string $invalid, string $valid)
    {
        $source = Source::create(['foo' => $valid, 'bar' => $invalid]);
        $this->assertToken($valid, $source);
        $this->assertNull(self::$replacement->token('bar', $source));
        $this->assertNull(self::$replacement->token('bar-fallback', $source));
    }

    public function valueExamples(): array
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function assertToken(string $value, Source $source): void
    {
        $token    = self::$replacement->token('foo', $source);
        $expected = Token\CompositeToken::withValueToken(
            new Token\BasicToken('foo', $value),
            new Token\BasicToken('foo.esc', str_replace('\\', '\\\\', $value))
        );

        $this->assertEquals($expected, $token);
    }
}
