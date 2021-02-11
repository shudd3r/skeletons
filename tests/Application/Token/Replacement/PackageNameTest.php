<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Replacement\PackageName;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;

class PackageNameTest extends TestCase
{
    public function testWithPackageNameInComposerJson_InitialTokenValue_IsReadFromComposerJson()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"name": "composer/package"}');

        $token = $this->replacement($env)->initialToken('package.name', []);
        $this->assertToken($token, 'composer/package', 'Composer/Package');
    }

    public function testWithoutPackageNameInComposerJson_InitialTokenValue_IsResolvedFromDirectoryStructure()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->path = 'D:\dev\www\project\directory';

        $token = $this->replacement($env)->initialToken('package.name', []);
        $this->assertToken($token, 'project/directory', 'Project/Directory');
    }

    public function testInitialToken_IsCached()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"name": "composer/package"}');

        $replacement = $this->replacement($env);
        $this->assertSame($replacement->initialToken('package.name', []), $replacement->initialToken('anything', []));
    }

    public function testTokenFactoryMethods_ReturnCorrectToken()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = '{"package.name": "source/package"}';
        $env->package()->path = 'package-root\path';

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('package.name', []), 'package-root/path', 'Package-root/Path');
        $this->assertToken($replacement->validationToken('package.name'), 'source/package', 'Source/Package');
        $this->assertToken($replacement->updateToken('package.name', []), 'source/package', 'Source/Package');
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethods_ValidatesValue(string $invalid, string $valid)
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = '{"package.name": "'. $invalid .'"}';
        $env->package()->addFile('composer.json', '{"name": "'. $invalid .'"}');

        $replacement = $this->replacement($env);
        $this->assertNull($replacement->initialToken('package.name', []));
        $this->assertNull($replacement->validationToken('package.name'));
        $this->assertNull($replacement->updateToken('package.name', []));

        $env = new Doubles\FakeRuntimeEnv();
        $env->metaDataFile()->contents = '{"package.name": "'. $valid .'"}';
        $env->package()->addFile('composer.json', '{"name": "'. $valid .'"}');

        $replacement = $this->replacement($env);
        $this->assertInstanceOf(Token::class, $replacement->initialToken('package.name', []));
        $this->assertInstanceOf(Token::class, $replacement->validationToken('package.name'));
        $this->assertInstanceOf(Token::class, $replacement->updateToken('package.name', []));
    }

    public function valueExamples()
    {
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    private function assertToken(Token $token, string $value, string $title, string $name = 'package.name'): void
    {
        $subToken = new Token\ValueToken($name . '.title', $title);
        $expected = new Token\CompositeValueToken($name, $value, $subToken);
        $this->assertEquals($expected, $token);
    }

    private function replacement(Doubles\FakeRuntimeEnv $env): PackageName
    {
        return new PackageName($env);
    }
}
