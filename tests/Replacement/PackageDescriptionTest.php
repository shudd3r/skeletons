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
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\ReplacementReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionTest extends TestCase
{
    public function testInputNames()
    {
        $replacement = new PackageDescription();
        $this->assertSame('desc', $replacement->optionName());
        $this->assertSame('Package description', $replacement->inputPrompt());
    }

    public function testWithDescriptionInComposerJson_DefaultValue_IsReadFromComposerJson()
    {
        $replacement = new PackageDescription();
        $env         = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"description": "composer json description"}');

        $this->assertSame('composer json description', $replacement->defaultValue($env, [], new Token\Replacements()));
    }

    public function testWithoutDescriptionInComposerJson_DefaultValue_IsResolvedFromFallbackReplacement()
    {
        $replacement = new PackageDescription('fallback.token');
        $env         = new Doubles\FakeRuntimeEnv();
        $env->package()->addFile('composer.json', '{"name": "composer/package"}');

        $fakeReplacement = new ReplacementReader($env, new Doubles\FakeReplacement('fallback value'));
        $fallback        = new Token\Replacements(['fallback.token' => $fakeReplacement]);

        $this->assertSame('fallback value package', $replacement->defaultValue($env, [], $fallback));
    }

    public function testTokenMethodWithValidValue_ReturnsExpectedToken()
    {
        $replacement = new PackageDescription();
        $expected    = new Token\ValueToken('token.name', 'package description');
        $this->assertEquals($expected, $replacement->token('token.name', 'package description'));
        $this->assertTrue($replacement->isValid('package description'));
    }

    public function testEmptyValue_IsInvalid()
    {
        $replacement = new PackageDescription();
        $this->assertFalse($replacement->isValid(''));
        $this->assertNull($replacement->token('token.name', ''));
    }
}
