<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Replacement\SrcNamespace;
use Shudd3r\PackageFiles\Replacement\PackageName;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class SrcNamespaceTest extends TestCase
{
    public function testWithSrcNamespaceInComposerJson_InitialTokenValue_IsReadFromComposerJson()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', $this->composerData());

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('src.namespace', []), 'Composer\\Namespace');
    }

    public function testWithoutSrcNamespaceInComposerJson_InitialTokenValue_IsResolvedFromPackageName()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', $this->composerData(false));

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('src.namespace', []), 'Package\\Name');
    }

    public function testTokenFactoryMethods_CreateCorrectToken()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->write(json_encode(['src.namespace' => 'Meta\\Namespace']));
        $env->package()->addFile('composer.json', $this->composerData());

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('ns.placeholder', []), 'Composer\\Namespace', 'ns.placeholder');
        $this->assertToken($replacement->validationToken('src.namespace'), 'Meta\\Namespace');
        $this->assertToken($replacement->updateToken('src.namespace', []), 'Meta\\Namespace');
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethod_ValidatesValue(string $invalid, string $valid)
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->write(json_encode(['ns.placeholder' => $invalid]));
        $options = ['ns' => $invalid];

        $replacement = $this->replacement($env);
        $this->assertNull($replacement->initialToken('ns.placeholder', $options));
        $this->assertNull($replacement->validationToken('ns.placeholder'));
        $this->assertNull($replacement->updateToken('ns.placeholder', $options));

        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->write(json_encode(['ns.placeholder' => $valid]));
        $options = ['ns' => $valid];

        $replacement = $this->replacement($env);
        $this->assertInstanceOf(Token::class, $replacement->initialToken('ns.placeholder', $options));
        $this->assertInstanceOf(Token::class, $replacement->validationToken('ns.placeholder'));
        $this->assertInstanceOf(Token::class, $replacement->updateToken('ns.placeholder', $options));
    }

    public function valueExamples(): array
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function assertToken(Token $token, string $value, string $name = 'src.namespace'): void
    {
        $subToken = new Token\ValueToken($name . '.esc', str_replace('\\', '\\\\', $value));
        $expected = new Token\CompositeValueToken($name, $value, $subToken);
        $this->assertEquals($expected, $token);
    }

    private function replacement(Doubles\FakeRuntimeEnv $env): SrcNamespace
    {
        $env->replacements()->add('fallback', new PackageName($env));
        return new SrcNamespace($env, 'fallback');
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