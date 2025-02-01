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
    private static Token                 $token;
    private static Fixtures\ExampleFiles $files;

    public static function setUpBeforeClass(): void
    {
        self::$token = new Token\BasicToken('replace.me', 'replaced');
        self::$files = new Fixtures\ExampleFiles('json-merge-example');
    }

    public function test_decorated_template_is_rendered_with_provided_Token(): void
    {
        $template = ['foo' => '{replace.me}', 'bar' => 'value'];
        $package  = ['baz' => 'merged'];
        $expected = ['foo' => 'replaced', 'bar' => 'value', 'baz' => 'merged'];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_for_flat_arrays_it_returns_merged_json_matching_flat_structure(): void
    {
        $template = ['first' => 'template_first', 'bar' => 'template_bar'];
        $package  = ['foo' => 'package_foo', 'bar' => 'package_bar'];
        $expected = ['first' => 'template_first', 'bar' => 'template_bar', 'foo' => 'package_foo'];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_null_values_after_merge_are_filtered(): void
    {
        $template = ['first' => null, 'bar' => null];
        $package  = ['foo' => 'package_foo', 'bar' => 'package_bar'];
        $expected = ['bar' => 'package_bar', 'foo' => 'package_foo'];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_for_nested_arrays_it_returns_merged_json_matching_nested_structure(): void
    {
        $template = ['first' => 'tpl', 'bar' => ['nest1' => 'tpl', 'nest2' => null, 'nest3' => 'tpl']];
        $package  = ['first' => 'pkg', 'bar' => ['nest1' => 'pkg', 'nest2' => 'pkg', 'nest4' => 'pkg']];
        $expected = ['first' => 'tpl', 'bar' => ['nest1' => 'tpl', 'nest2' => 'pkg', 'nest3' => 'tpl', 'nest4' => 'pkg']];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_for_nested_lists_it_returns_lists_with_combined_values(): void
    {
        $template = ['list' => ['foo', 'bar', 'baz']];
        $package  = ['list' => ['package1', 'baz', 'foo', 'package2']];
        $expected = ['list' => ['foo', 'bar', 'baz', 'package1', 'package2']];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    /** @dataProvider nonStructuralContents */
    public function test_without_json_template_it_returns_template_render(string $contents): void
    {
        $this->assertSame($contents, $this->jsonTemplate($contents, 'not {json}')->render(self::$token));
        $this->assertSame($contents, $this->jsonTemplate($contents, '{"foo": "bar"}')->render(self::$token));
    }

    public function test_without_json_structure_in_package_it_returns_filtered_template_render(): void
    {
        $this->assertJsonData(['foo' => 'bar'], $this->jsonTemplate('{"foo": "bar", "baz": null}', 'not json'));

        $template = $this->examplePackageTemplate('not-existing.json', false);
        $token    = $this->token('package/name', 'Initial package description', 'MyProject\\\\Namespace', 'initial@example.com');
        $expected = self::$files->contentsOf('initialized-from-empty.json');
        $this->assertSame($expected, $template->render($token));
    }

    /** @dataProvider mismatchedDataTypes */
    public function test_for_not_matching_types_package_values_are_ignored(array $template, array $package, ?array $expected): void
    {
        $expected ??= $template;
        $this->assertJsonData($expected, $this->template($template, $package));

        $template = ['sub' => $template];
        $package  = ['sub' => $package];
        $expected = $expected !== [] ? ['sub' => $expected] : [];
        $this->assertJsonData($expected, $this->template($template, $package));

        $expand = ['valid' => 'value'];

        $expTemplate = array_merge($template, $expand);
        $this->assertJsonData(array_merge($expected, $expand), $this->template($expTemplate, $package));

        $expTemplate = array_merge($expand, $template);
        $this->assertJsonData(array_merge($expand, $expected), $this->template($expTemplate, $package));

        $expPackage = array_merge($package, $expand);
        $expected   = array_merge($expected, $expand);
        $this->assertJsonData($expected, $this->template($template, $expPackage));

        $expPackage = array_merge($expand, $package);
        $this->assertJsonData($expected, $this->template($template, $expPackage));
    }

    public function test_first_array_in_template_list_is_used_as_structure_template_for_all_items(): void
    {
        $template = ['list' => [['a' => 1, 'b' => 1]]];
        $package  = ['list' => [['b' => 2, 'a' => 2, 'c' => 2], ['c' => 3, 'a' => 3], ['b' => 1, 'a' => 1]]];
        $expected = ['list' => [['a' => 1, 'b' => 1], ['a' => 2, 'b' => 2, 'c' => 2], ['a' => 3, 'c' => 3]]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_null_values_in_template_item_are_filtered_after_merge(): void
    {
        $template = [['a' => null, 'b' => 1, 'c' => null]];
        $package  = [['b' => 2, 'a' => 2, 'c' => 2], ['c' => 3, 'a' => 3], ['b' => 1]];
        $expected = [['b' => 1], ['a' => 2, 'b' => 2, 'c' => 2], ['a' => 3, 'c' => 3]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_template_item_with_only_null_values_is_not_added(): void
    {
        $template = [['a' => null, 'b' => null]];
        $package  = [['b' => 1, 'a' => 1, 'c' => 1], ['c' => 2, 'a' => 2]];
        $expected = [['a' => 1, 'b' => 1, 'c' => 1], ['a' => 2, 'c' => 2]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_empty_template_list_after_merge_is_filtered(): void
    {
        $template = ['foo' => 'foo-value', 'list' => [['a' => null, 'b' => null]]];
        $package  = ['bar' => 'bar-value'];
        $expected = ['foo' => 'foo-value', 'bar' => 'bar-value'];
        $this->assertJsonData($expected, $this->template($template, $package));

        $package = ['bar' => 'bar-value', 'list' => [['type', 'mismatch']]];
        $this->assertJsonData($expected, $this->template($template, $package));
    }

    public function test_type_of_empty_structure_after_merge_is_based_on_template_type(): void
    {
        $template = json_encode(['first' => null, 'bar' => null]);
        $this->assertSame('{}', trim($this->jsonTemplate($template, '[]')->render(self::$token)));

        $template = json_encode([['a' => null, 'b' => null]]);
        $this->assertSame('[]', trim($this->jsonTemplate($template, '{}')->render(self::$token)));
    }

    public function test_updated_keys_in_synchronized_structures_are_merged(): void
    {
        $template = ['foo' => null, 'updated_key' => 'something'];
        $package  = ['foo' => 'value', 'old_key' => 'something', 'bar' => 'value'];

        $initialMerge = ['foo' => 'value', 'updated_key' => 'something', 'old_key' => 'something', 'bar' => 'value'];
        $this->assertJsonData($initialMerge, $this->template($template, $package));

        $synchronizedMerge = ['foo' => 'value', 'updated_key' => 'something', 'bar' => 'value'];
        $this->assertJsonData($synchronizedMerge, $this->template($template, $package, true));
    }

    public function test_example_composer_json_file_initialization(): void
    {
        $template = $this->examplePackageTemplate('package-composer.json', false);
        $token    = $this->token('package/name', 'Initial package description', 'MyProject\\\\Namespace', 'initial@example.com');

        $expected = self::$files->contentsOf('initialized-composer.json');
        $this->assertSame($expected, $template->render($token));
    }

    public function test_without_synchronization_flag_updated_key_is_addedd(): void
    {
        $template = $this->examplePackageTemplate('initialized-composer.json', false);
        $token    = $this->token('new-package/name', 'Updated description', 'MyProject\\\\UpdatedNamespace', 'updated@example.com');

        $expected = self::$files->contentsOf('update-not-synchronized.json');
        $this->assertSame($expected, $template->render($token));
    }

    public function test_with_synchronization_flag_updated_key_is_replaced(): void
    {
        $template = $this->examplePackageTemplate('initialized-composer.json', true);
        $token    = $this->token('new-package/name', 'Updated description', 'MyProject\\\\UpdatedNamespace', 'updated@example.com');

        $expected = self::$files->contentsOf('update-synchronized.json');
        $this->assertSame($expected, $template->render($token));
    }

    public static function nonStructuralContents(): iterable
    {
        return [
            'empty string'     => [''],
            'non-json string'  => ['some non-json contents'],
            'simple type json' => ['123']
        ];
    }

    public static function mismatchedDataTypes(): iterable
    {
        $assoc = ['foo' => 'a', 'bar' => 'b'];
        $tpl   = ['foo' => null, 'bar' => null];
        $list  = ['c', 'd'];
        $val   = 'value';

        return [
            'assoc-list'              => [$assoc, $list, null],
            'list-assoc'              => [$list, $assoc, null],
            'list:assoc-val'          => [[$assoc, $assoc], [$val, $val], null],
            'list:list-val'           => [[$list, $list], [$val, $val], null],
            'list:assoc-list'         => [[$assoc, $assoc], [$list, $list], null],
            'list:list-assoc'         => [[$list, $list], [$assoc, $assoc], null],
            'list:tpl-list'           => [[$tpl], [$list, $list], []],
            'list:tpl+assoc-list'     => [[$tpl, $assoc], [$list, $list], [$assoc]],
            'sub assoc-val'           => [['foo' => $assoc], ['foo' => $val], null],
            'sub list-val'            => [['foo' => $list], ['foo' => $val], null]
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

        return $this->jsonTemplate($template, $package, $synchronized);
    }

    private function template(array $template, array $package, bool $synchronized = false): Template
    {
        return $this->jsonTemplate(json_encode($template), json_encode($package), $synchronized);
    }

    private function jsonTemplate(string $template, string $package, bool $synchronized = false): Template
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
