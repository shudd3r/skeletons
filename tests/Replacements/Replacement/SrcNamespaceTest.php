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
    public function testWithoutEnoughDataToResolveValue_TokenMethod_ReturnsNull()
    {
        $replacement = new SrcNamespace('bar');

        $source = Source::create();
        $this->assertNull($replacement->token('foo', $source));
    }

    public function testWithCorrectFallbackValue_TokenValueIsResolvedFromFallback()
    {
        $replacement = new SrcNamespace('bar');

        $source = Source::create()->withFallbackTokenValue('bar', 'invalid fallback value');
        $this->assertNull($replacement->token('foo', $source));

        $source = Source::create()->withFallbackTokenValue('bar', 'fallback/package-name');
        $this->assertToken('Fallback\\PackageName', $replacement->token('foo', $source));
    }

    public function testWithMatchingNamespaceInComposerJsonFile_TokenValueIsResolvedWithComposerData()
    {
        $replacement  = new SrcNamespace('bar');
        $composerData = fn (string $directory) => ['autoload' => ['psr-4' => ['Composer\\Namespace\\' => $directory]]];
        $source       = Source::create()->withFallbackTokenValue('bar', 'fallback/package-name');

        $source = $source->withComposerData($composerData('app/'));
        $this->assertToken('Fallback\\PackageName', $replacement->token('foo', $source));

        $source = $source->withComposerData($composerData('src/'));
        $this->assertToken('Composer\\Namespace', $replacement->token('foo', $source));
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testResolvedTokenValue_IsValidated(string $invalid, string $valid)
    {
        $replacement = new SrcNamespace();

        $source = Source::create(['foo' => $valid, 'bar' => $invalid]);
        $this->assertToken($valid, $replacement->token('foo', $source));
        $this->assertNull($replacement->token('bar', $source));
    }

    public function valueExamples(): array
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function assertToken(string $value, Token $token): void
    {
        $expected = Token\CompositeToken::withValueToken(
            new Token\BasicToken('foo', $value),
            new Token\BasicToken('foo.esc', str_replace('\\', '\\\\', $value))
        );

        $this->assertEquals($expected, $token);
    }
}
