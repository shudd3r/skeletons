<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Template;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Template\ComposerJsonTemplate;
use Shudd3r\PackageFiles\Tests\Doubles;


class ComposerJsonTemplateTest extends TestCase
{
    public function testMissingComposerFileIsCreatedWithPropertiesValues()
    {
        $template       = new ComposerJsonTemplate(new Doubles\MockedFile('', false));
        $renderedString = $template->render(new Doubles\FakeTokens());
        $this->assertSame($this->composerJsonForDefaultValues(), $renderedString);
    }

    public function testEmptyComposerFileIsFilledWithPropertiesValues()
    {
        $renderedString = $this->template('{}')->render(new Doubles\FakeTokens());
        $this->assertEquals($this->composerJsonForDefaultValues(), $renderedString);
    }

    public function testFieldsAreReturnedInCorrectOrder()
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

        $renderedString    = $this->template(json_encode($template))->render(new Doubles\FakeTokens());
        $composerArrayKeys = array_keys(json_decode($renderedString, true));

        $expectedKeys = [
            'name', 'description', 'type', 'license', 'authors', 'autoload', 'autoload-dev',
            'minimum-stability', 'require', 'require-dev'
        ];

        $this->assertEquals($expectedKeys, $composerArrayKeys);
    }

    public function testSrcAutoloadIsUpdated()
    {
        $autoload = json_encode([
            'autoload'     => ['foo' => ['bar'], 'psr-4' => ['Foo\\Namespace\\' => 'src/']],
            'autoload-dev' => ['psr-4' => ['Foo\\Namespace\\Tests' => 'tests/']]
        ]);

        $renderedString = $this->template($autoload)->render(new Doubles\FakeTokens());
        $composerArray  = json_decode($renderedString, true);

        $expected = [
            'autoload'     => ['psr-4' => ['Polymorphine\\Dev\\' => 'src/'], 'foo' => ['bar']],
            'autoload-dev' => ['psr-4' => ['Polymorphine\\Dev\\Tests\\' => 'tests/']]
        ];

        $this->assertSame($expected['autoload'], $composerArray['autoload']);
        $this->assertSame($expected['autoload-dev'], $composerArray['autoload-dev']);
    }

    private function template(string $contents)
    {
        return new ComposerJsonTemplate(new Doubles\MockedFile($contents));
    }

    private function composerJsonForDefaultValues(): string
    {
        return <<<'JSON'
            {
                "name": "polymorphine/dev",
                "description": "Package description",
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
                        "Polymorphine\\Dev\\": "src/"
                    }
                },
                "autoload-dev": {
                    "psr-4": {
                        "Polymorphine\\Dev\\Tests\\": "tests/"
                    }
                },
                "minimum-stability": "stable"
            }
            
            JSON;
    }
}
