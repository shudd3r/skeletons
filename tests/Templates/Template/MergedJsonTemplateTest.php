<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Templates\Template;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Tests\Fixtures;


class MergedJsonTemplateTest extends TestCase
{
    private static Token $token;
    private static Fixtures\ExampleFiles $files;

    public static function setUpBeforeClass(): void
    {
        self::$token = new Token\BasicToken('replace.me', 'replaced');
        self::$files = new Fixtures\ExampleFiles('json-merge-example');
    }

    /**
     * @dataProvider nonStructuralContents
     */
    public function testWithoutContentsToMerge_ReturnsOriginalTemplateRender(string $contents)
    {
        $this->assertSame($contents, $this->template($contents, '')->render(self::$token));
        $this->assertSame('not json', $this->template('not json', $contents)->render(self::$token));
    }

    public function testDecoratedTemplate_IsRenderedWithProvidedToken()
    {
        $template = json_encode(['foo' => '{replace.me}', 'bar' => 'value']);
        $package  = json_encode(['baz' => 'merged']);
        $expected = ['foo' => 'replaced', 'bar' => 'value', 'baz' => 'merged'];
        $this->assertJsonData($expected, $this->template($template, $package));
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

    public function testForNestedLists_ReturnsListsWithCombinedValues()
    {
        $template = json_encode(['list' => ['foo', 'bar', 'baz']]);
        $package  = json_encode(['list' => ['package1', 'baz', 'foo', 'package2']]);
        $expected = ['list' => ['foo', 'bar', 'baz', 'package1', 'package2']];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testFirstArrayInTemplateList_IsUsedAsStructureTemplateForAllItems()
    {
        $template = json_encode(['list' => [['a' => 1, 'b' => 1]]]);
        $package  = json_encode(['list' => [['b' => 2, 'a' => 2, 'c' => 2], ['c' => 3, 'a' => 3], ['b' => 1, 'a' => 1]]]);
        $expected = ['list' => [['a' => 1, 'b' => 1], ['a' => 2, 'b' => 2, 'c' => 2], ['a' => 3, 'c' => 3]]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testFirstArrayInTemplateListWithNullValues_IsCombinedWithItemsWithoutNullValues()
    {
        $template = json_encode([['a' => null, 'b' => 1, 'c' => null]]);
        $package  = json_encode([['b' => 2, 'a' => 2, 'c' => 2], ['c' => 3, 'a' => 3], ['b' => 1]]);
        $expected = [['b' => 1], ['a' => 2, 'b' => 2, 'c' => 2], ['a' => 3, 'c' => 3]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testFirstArrayInListWithNullValuesOnly_IsUsedOnlyAsTemplateAndNotMergedIntoList()
    {
        $template = json_encode([['a' => null, 'b' => null]]);
        $package  = json_encode([['b' => 1, 'a' => 1, 'c' => 1], ['c' => 2, 'a' => 2]]);
        $expected = [['a' => 1, 'b' => 1, 'c' => 1], ['a' => 2, 'c' => 2]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testEmptyTemplateListAfterMerge_IsFiltered()
    {
        $template = json_encode(['foo' => 'foo-value', 'list' => [['a' => null, 'b' => null]]]);
        $package  = json_encode(['bar' => 'bar-value']);
        $expected = ['foo' => 'foo-value', 'bar' => 'bar-value'];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function testTypeOfEmptyStructureAfterMerge_IsBasedOnTemplateType()
    {
        $template = json_encode(['first' => null, 'bar' => null]);
        $this->assertSame('{}', trim($this->template($template, '[]')->render(self::$token)));

        $template = json_encode([['a' => null, 'b' => null]]);
        $this->assertSame('[]', trim($this->template($template, '{}')->render(self::$token)));
    }

    public function testUpdatedKeysInSynchronizedStructures_AreMerged()
    {
        $template = json_encode(['foo' => null, 'updated_key' => 'something']);
        $package  = json_encode(['foo' => 'value', 'old_key' => 'something', 'bar' => 'value']);

        $initialMerge = ['foo' => 'value', 'updated_key' => 'something', 'old_key' => 'something', 'bar' => 'value'];
        $this->assertJsonData($initialMerge, $this->template($template, $package));

        $synchronizedMerge = ['foo' => 'value', 'updated_key' => 'something', 'bar' => 'value'];
        $this->assertJsonData($synchronizedMerge, $this->template($template, $package, true));
    }

    public function testExampleComposerJsonFileInitialization()
    {
        $template = $this->examplePackageTemplate('package-composer.json', false);
        $token    = $this->token('package/name', 'Initial package description', 'MyProject\\\\Namespace', 'initial@example.com');

        $expected = self::$files->contentsOf('initialized-composer.json');
        $this->assertSame($expected, $template->render($token));
    }

    public function testWithoutSynchronizationFlag_UpdatedKeyIsAdded()
    {
        $template = $this->examplePackageTemplate('initialized-composer.json', false);
        $token    = $this->token('new-package/name', 'Updated description', 'MyProject\\\\UpdatedNamespace', 'updated@example.com');

        $expected = self::$files->contentsOf('update-not-synchronized.json');
        $this->assertSame($expected, $template->render($token));
    }

    public function testWithSynchronizationFlag_UpdatedKeyIsReplaced()
    {
        $template = $this->examplePackageTemplate('initialized-composer.json', true);
        $token    = $this->token('new-package/name', 'Updated description', 'MyProject\\\\UpdatedNamespace', 'updated@example.com');

        $expected = self::$files->contentsOf('update-synchronized.json');
        $this->assertSame($expected, $template->render($token));
    }

    public function nonStructuralContents(): array
    {
        return [
            'empty string'     => [''],
            'non-json string'  => ['some non-json contents'],
            'simple type json' => ['123']
        ];
    }

    private function assertJsonData(array $expected, Template $json): void
    {
        $this->assertSame($expected, json_decode($json->render(self::$token), true));
    }

    private function examplePackageTemplate(string $packageStateFile, bool $synchronized): Template
    {
        $template = self::$files->contentsOf('template-composer.json');
        $package  = self::$files->contentsOf($packageStateFile);

        return $this->template($template, $package, $synchronized);
    }

    private function template(string $template, string $package, bool $synchronized = false): Template
    {
        $template = new Template\BasicTemplate($template);
        return new Template\MergedJsonTemplate($template, $package, $synchronized);
    }

    private function token(string $packageName, string $description, string $namespace, string $email): Token
    {
        return new Token\CompositeToken(
            new Token\BasicToken('package.name', $packageName),
            new Token\BasicToken('package.description', $description),
            new Token\BasicToken('namespace.src.esc', $namespace),
            new Token\BasicToken('author.email', $email)
        );
    }
}
