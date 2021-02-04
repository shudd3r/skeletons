<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\ReaderFactory;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\ReaderFactory\SrcNamespaceReaderFactory;
use Shudd3r\PackageFiles\Application\Token\ReaderFactory\PackageNameReaderFactory;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class SrcNamespaceReaderFactoryTest extends TestCase
{
    public function testWithSrcNamespaceInComposerJson_InitialTokenValue_IsReadFromComposerJson()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', $this->composerData());
        $replacement = $this->replacement($env);

        $this->assertToken($replacement->initialToken('src.namespace'), 'Composer\\Namespace');
    }

    public function testWithoutSrcNamespaceInComposerJson_InitialTokenValue_IsResolvedFromPackageName()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', $this->composerData(false));
        $replacement = $this->replacement($env);

        $this->assertToken($replacement->initialToken('src.namespace'), 'Package\\Name');
    }

    public function testTokenFactoryMethods_CreateCorrectToken()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = json_encode(['src.namespace' => 'Meta\\Namespace']);
        $env->package()->addFile('composer.json', $this->composerData());

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('ns.placeholder'), 'Composer\\Namespace', 'ns.placeholder');
        $this->assertToken($replacement->validationToken('src.namespace'), 'Meta\\Namespace');
        $this->assertToken($replacement->updateToken('src.namespace'), 'Meta\\Namespace');
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
        $env->metaDataFile()->contents = json_encode(['ns.placeholder' => $invalid]);
        $replacement = $this->replacement($env, ['ns' => $invalid]);

        $this->assertNull($replacement->initialToken('ns.placeholder'));
        $this->assertNull($replacement->validationToken('ns.placeholder'));
        $this->assertNull($replacement->updateToken('ns.placeholder'));

        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = json_encode(['ns.placeholder' => $valid]);
        $replacement = $this->replacement($env, ['ns' => $valid]);
        $this->assertInstanceOf(Token::class, $replacement->initialToken('ns.placeholder'));
        $this->assertInstanceOf(Token::class, $replacement->validationToken('ns.placeholder'));
        $this->assertInstanceOf(Token::class, $replacement->updateToken('ns.placeholder'));
    }

    public function valueExamples()
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    private function assertToken(Token $token, string $value, string $name = 'src.namespace')
    {
        $subToken = new Token\ValueToken($name . '.esc', str_replace('\\', '\\\\', $value));
        $expected = new Token\CompositeValueToken($name, $value, $subToken);
        $this->assertEquals($expected, $token);
    }

    private function replacement(Doubles\FakeRuntimeEnv $env, array $options = []): SrcNamespaceReaderFactory
    {
        return new SrcNamespaceReaderFactory($env, $options, new PackageNameReaderFactory($env, $options));
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
