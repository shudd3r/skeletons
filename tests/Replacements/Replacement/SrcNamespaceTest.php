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
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Tests\Doubles;


class SrcNamespaceTest extends TestCase
{
    public function testInputNames()
    {
        $replacement = new SrcNamespace();
        $this->assertSame('ns', $replacement->optionName());
        $this->assertSame('Source files namespace', $replacement->inputPrompt());
        $this->assertSame('Source files namespace [format: <vendor>\\<namespace>]', $replacement->description());
    }

    public function testWithSrcNamespaceInComposerJson_DefaultValue_IsReadFromComposerJson()
    {
        $replacement = new SrcNamespace();
        $fallback    = new Doubles\FakeFallbackReader();
        $env         = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', $this->composerData());

        $this->assertSame('Composer\\Namespace', $replacement->defaultValue($env, $fallback));
    }

    public function testWithoutSrcNamespaceInComposerJson_DefaultValue_IsResolvedFromFallbackReplacement()
    {
        $replacement = new SrcNamespace('fallback.name');
        $fallback    = new Doubles\FakeFallbackReader(['fallback.name' => 'fallback/name']);
        $env         = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', $this->composerData(false));

        $this->assertSame('Fallback\\Name', $replacement->defaultValue($env, $fallback));
    }

    public function testTokenMethodWithValidValue_ReturnsExpectedToken()
    {
        $replacement = new SrcNamespace();

        $expected = Token\CompositeToken::withValueToken(
            new Token\ValueToken('src.namespace', 'Source\\Namespace'),
            new Token\ValueToken('src.namespace.esc', 'Source\\\\Namespace')
        );

        $this->assertEquals($expected, $replacement->token('src.namespace', 'Source\\Namespace'));
        $this->assertTrue($replacement->isValid('Source\\Namespace'));
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethod_ValidatesValue(string $invalid, string $valid)
    {
        $replacement = new SrcNamespace();

        $this->assertFalse($replacement->isValid($invalid));
        $this->assertNull($replacement->token('package.name', $invalid));

        $this->assertTrue($replacement->isValid($valid));
        $this->assertInstanceOf(Token::class, $replacement->token('package.name', $valid));
    }

    public function valueExamples(): array
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function composerData(bool $withAutoload = true): string
    {
        $srcNamespace = $withAutoload ? ['Composer\\Namespace\\' => 'src/'] : [];
        $namespaces   = ['Some\\Namespace\\' => 'not/src/'] + $srcNamespace;
        $composer     = [
            'name' => 'package/name',
            'autoload' => ['psr-4' => $namespaces]
        ];

        return json_encode($composer);
    }
}
