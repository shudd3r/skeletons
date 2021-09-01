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
use Shudd3r\PackageFiles\Tests\Fixtures;


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

    public function testExampleComposerJsonFileNormalization()
    {
        $files = new Fixtures\ExampleFiles('composer-example');

        $template = $files->contentsOf('template-composer.json');
        $package  = $files->contentsOf('package-composer.json');
        $expected = $files->contentsOf('expected-composer.json');
        $this->assertSame($expected, $this->template($template, $package)->render(new Doubles\FakeToken()));
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
}
