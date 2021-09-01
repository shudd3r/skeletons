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
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Tests\Doubles;


class MergedJsonTemplateTest extends TestCase
{
    /**
     * @dataProvider possibleContents
     */
    public function testWithoutContentsToMerge_ReturnsOriginalRender(string $contents)
    {
        $this->assertSame($contents, $this->template($contents, '')->render(new Doubles\FakeToken()));
        $this->assertSame('not json', $this->template('not json', $contents)->render(new Doubles\FakeToken()));
    }

    public function testForFlatArrays_ReturnsMergedJsonMatchingTemplateStructure()
    {
        $template = json_encode(['first' => 'template_first', 'bar' => 'template_bar']);
        $package  = json_encode(['foo' => 'package_foo', 'bar' => 'package_bar']);
        $expected = ['first' => 'template_first', 'bar' => 'template_bar', 'foo' => 'package_foo'];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testNullValuesAfterMergeAreFiltered()
    {
        $template = json_encode(['first' => null, 'bar' => null]);
        $package  = json_encode(['foo' => 'package_foo', 'bar' => 'package_bar']);
        $expected = ['bar' => 'package_bar', 'foo' => 'package_foo'];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testForNestedArrays_ReturnsStructureWithNestedArraysMatchingTemplateStructure()
    {
        $template = json_encode(['first' => 'tpl', 'bar' => ['nest1' => 'tpl', 'nest2' => null, 'nest3' => 'tpl']]);
        $package  = json_encode(['first' => 'pkg', 'bar' => ['nest1' => 'pkg', 'nest2' => 'pkg', 'nest4' => 'pkg']]);
        $expected = ['first' => 'tpl', 'bar' => ['nest1' => 'tpl', 'nest2' => 'pkg', 'nest3' => 'tpl', 'nest4' => 'pkg']];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testComposerJsonFileNormalization()
    {
        $template = $this->template($this->templateComposerJson(), $this->packageComposerJson());
        $this->assertSame($this->mergedComposerJson(), $template->render(new Doubles\FakeToken()));
    }

    public function possibleContents(): array
    {
        return [
            'empty string' => [''],
            'non-json string' => ['some non-json contents'],
            'simple json' => [json_encode(['test' => 'json', 'foo' => 'bar'])]
        ];
    }

    private function assertJsonData(array $expected, Template $json)
    {
        $this->assertSame($expected, json_decode($json->render(new Doubles\FakeToken()), true));
    }

    private function template(string $rendered, string $json): Template
    {
        return new Template\MergedJsonTemplate(new Doubles\FakeTemplate($rendered), new Doubles\MockedFile($json));
    }

    private function templateComposerJson(): string
    {
        return <<<'JSON'
            {
                "name": "{package.name}",
                "description": "{package.description}",
                "type": null,
                "license": null,
                "authors": null,
                "minimum-stability": null,
                "require": null,
                "require-dev": null,
                "autoload": {
                    "psr-4": {
                        "{namespace.src.esc}\\": "src/"
                    }
                },
                "autoload-dev": {
                    "psr-4": {
                        "{namespace.src.esc}\\Tests\\": "tests/"
                    }
                }
            }
            
            JSON;
    }

    private function packageComposerJson(): string
    {
        return <<<'JSON'
            {
                "description": "Default description text",
                "license": "MIT",
                "type": "library",
                "authors": [
                    {
                        "name": "Shudd3r",
                        "email": "shudder@example.com"
                    }
                ],
                "autoload": {
                    "psr-4": {
                        "Library\\Namespace\\": "libs/src/"
                    },
                    "psr-0": {
                        "Monolog\\": ["src/", "lib/"]
                    }
                },
                "autoload-dev": {
                    "classmap": ["src/", "lib/", "Something.php"]
                },
                "minimum-stability": "stable",
                "require": {
                    "php": "^7.4",
                    "monolog/monolog": "2.0.*"
                },
                "require-dev": {
                    "some/package": "^1.0"
                }
            }
            
            JSON;
    }

    private function mergedComposerJson(): string
    {
        return <<<'JSON'
            {
                "name": "{package.name}",
                "description": "{package.description}",
                "type": "library",
                "license": "MIT",
                "authors": [
                    {
                        "name": "Shudd3r",
                        "email": "shudder@example.com"
                    }
                ],
                "minimum-stability": "stable",
                "require": {
                    "php": "^7.4",
                    "monolog/monolog": "2.0.*"
                },
                "require-dev": {
                    "some/package": "^1.0"
                },
                "autoload": {
                    "psr-4": {
                        "{namespace.src.esc}\\": "src/",
                        "Library\\Namespace\\": "libs/src/"
                    },
                    "psr-0": {
                        "Monolog\\": [
                            "src/",
                            "lib/"
                        ]
                    }
                },
                "autoload-dev": {
                    "psr-4": {
                        "{namespace.src.esc}\\Tests\\": "tests/"
                    },
                    "classmap": [
                        "src/",
                        "lib/",
                        "Something.php"
                    ]
                }
            }
            
            
            JSON;
    }
}
