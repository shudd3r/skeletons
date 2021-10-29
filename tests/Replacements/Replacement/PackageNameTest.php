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
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Tests\Doubles;


class PackageNameTest extends TestCase
{
    public function testInputNames()
    {
        $replacement = new PackageName();
        $this->assertSame('package', $replacement->optionName());
        $this->assertSame('Packagist package name', $replacement->inputPrompt());
    }

    public function testWithPackageNameInComposerJson_DefaultValue_IsReadFromComposerJson()
    {
        $replacement = new PackageName();
        $fallback    = new Doubles\FakeFallbackReader();
        $env         = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"name": "composer/package"}');

        $this->assertSame('composer/package', $replacement->defaultValue($env, $fallback));
    }

    public function testWithoutPackageNameInComposerJson_DefaultValue_IsResolvedFromDirectoryStructure()
    {
        $replacement = new PackageName();
        $fallback    = new Doubles\FakeFallbackReader();
        $env         = new Doubles\FakeRuntimeEnv(new Doubles\FakeDirectory('www/dev/project/directory'));
        $this->assertSame('project/directory', $replacement->defaultValue($env, $fallback));
    }

    public function testTokenMethodWithValidValue_ReturnsExpectedToken()
    {
        $replacement = new PackageName();

        $subToken = new Token\ValueToken('package.name.title', 'Package/Name');
        $expected = new Token\CompositeValueToken('package.name', 'package/name', $subToken);

        $this->assertEquals($expected, $replacement->token('package.name', 'package/name'));
        $this->assertTrue($replacement->isValid('package/name'));
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testTokenFactoryMethods_ValidatesValue(string $invalid, string $valid)
    {
        $replacement = new PackageName();

        $this->assertFalse($replacement->isValid($invalid));
        $this->assertNull($replacement->token('package.name', $invalid));

        $this->assertTrue($replacement->isValid($valid));
        $this->assertInstanceOf(Token::class, $replacement->token('package.name', $valid));
    }

    public function valueExamples(): array
    {
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }
}
