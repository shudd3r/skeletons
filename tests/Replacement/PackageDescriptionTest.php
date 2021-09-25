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
use Shudd3r\PackageFiles\Replacement\PackageDescription;
use Shudd3r\PackageFiles\Replacement\PackageName;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionTest extends TestCase
{
    public function testWithDescriptionInComposerJson_InitialTokenValue_IsReadFromComposerJson()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"description": "composer json description"}');

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('package.desc', []), 'composer json description');
    }

    public function testWithoutDescriptionInComposerJson_InitialTokenValue_IsResolvedFromPackageName()
    {
        $env = new Doubles\FakeRuntimeEnv(new Doubles\FakeDirectory('root/package/path'));

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('package.desc', []), 'package/path package');
    }

    public function testTokenFactoryMethods_CreateCorrectToken()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"description": "composer desc"}');
        $env->metaDataFile()->write('{"desc.placeholder": "meta desc"}');

        $replacement = $this->replacement($env);
        $this->assertToken($replacement->initialToken('desc.placeholder', []), 'composer desc', 'desc.placeholder');
        $this->assertToken($replacement->validationToken('desc.placeholder'), 'meta desc', 'desc.placeholder');
        $this->assertToken($replacement->updateToken('desc.placeholder', []), 'meta desc', 'desc.placeholder');
    }

    public function testForEmptyTokenValue_TokenFactoryMethods_ReturnNull()
    {
        $env = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"description": ""}');
        $env->metaDataFile()->write('{"desc.placeholder": ""}');

        $replacement = $this->replacement($env);
        $this->assertNull($replacement->initialToken('foo', []));
        $this->assertNull($replacement->validationToken('desc.placeholder'));
        $this->assertNull($replacement->updateToken('not.existing.data', []));
    }

    private function assertToken(Token $token, string $value, string $name = 'package.desc'): void
    {
        $expected = new Token\ValueToken($name, $value);
        $this->assertEquals($expected, $token);
    }

    private function replacement(Doubles\FakeRuntimeEnv $env): PackageDescription
    {
        return new PackageDescription($env, new PackageName($env));
    }
}
