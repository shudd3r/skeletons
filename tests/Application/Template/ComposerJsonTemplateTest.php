<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Template;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Template\ComposerJsonTemplate;
use Shudd3r\PackageFiles\Application\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class ComposerJsonTemplateTest extends TestCase
{
    public function testMissingComposerFile_IsCreatedWithDefaultValues()
    {
        $template       = new ComposerJsonTemplate(new Doubles\MockedFile(null));
        $renderedString = $template->render($this->tokens());
        $this->assertSame($this->composerJsonForDefaultValues(), $renderedString);
    }

    public function testEmptyComposerFile_IsFilledWithDefaultTokenValues()
    {
        $renderedString = $this->template('{}')->render($this->tokens());
        $this->assertEquals($this->composerJsonForDefaultValues(), $renderedString);
    }

    public function testFields_AreReturnedInCorrectOrder()
    {
        $template = [
            'require'           => [],
            'require-dev'       => [],
            'type'              => 'xxx',
            'license'           => 'xxx',
            'name'              => 'xxx',
            'description'       => 'xxx',
            'autoload'          => [],
            'autoload-dev'      => [],
            'authors'           => [['name' => 'Shudd3r', 'email' => 'q3.shudder@gmail.com']],
            'minimum-stability' => 'stable'
        ];

        $renderedString    = $this->template(json_encode($template))->render($this->tokens());
        $composerArrayKeys = array_keys(json_decode($renderedString, true));

        $expectedKeys = [
            'name', 'description', 'type', 'license', 'authors', 'autoload', 'autoload-dev',
            'minimum-stability', 'require', 'require-dev'
        ];

        $this->assertEquals($expectedKeys, $composerArrayKeys);
    }

    public function testSrcAutoload_IsUpdated()
    {
        $autoload = json_encode([
            'autoload'     => ['foo' => ['bar'], 'psr-4' => ['Foo\\Namespace\\' => 'src/']],
            'autoload-dev' => ['psr-4' => ['Foo\\Namespace\\Tests' => 'tests/']]
        ]);

        $renderedString = $this->template($autoload)->render($this->tokens());
        $composerArray  = json_decode($renderedString, true);

        $expected = [
            'autoload'     => ['psr-4' => ['Main\\Namespace\\' => 'src/'], 'foo' => ['bar']],
            'autoload-dev' => ['psr-4' => ['Main\\Namespace\\Tests\\' => 'tests/']]
        ];

        $this->assertSame($expected['autoload'], $composerArray['autoload']);
        $this->assertSame($expected['autoload-dev'], $composerArray['autoload-dev']);
    }

    private function template(string $contents)
    {
        return new ComposerJsonTemplate(new Doubles\MockedFile($contents));
    }

    private function tokens(): Token
    {
        return new Token\CompositeToken(
            new Token\ValueToken('{package.name}', 'package/name'),
            new Token\ValueToken('{description.text}', 'Description text'),
            new Token\ValueToken('{namespace.src.esc}', 'Main\\\\Namespace')
        );
    }

    private function composerJsonForDefaultValues(): string
    {
        return <<<'JSON'
            {
                "name": "package/name",
                "description": "Description text",
                "type": "library",
                "license": "MIT",
                "authors": [
                    {
                        "name": "Shudd3r",
                        "email": "q3.shudder@gmail.com"
                    }
                ],
                "autoload": {
                    "psr-4": {
                        "Main\\Namespace\\": "src/"
                    }
                },
                "autoload-dev": {
                    "psr-4": {
                        "Main\\Namespace\\Tests\\": "tests/"
                    }
                },
                "minimum-stability": "stable"
            }
            
            JSON;
    }
}
