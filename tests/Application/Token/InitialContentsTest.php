<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\InitialContents;


class InitialContentsTest extends TestCase
{
    public function testForTemplateWithoutPlaceholders_ReturnsUnchangedTemplate()
    {
        $token    = new InitialContents();
        $template = 'example template {some.token} content';

        $this->assertSame($template, $token->replacePlaceholders($template));
    }

    public function testForTemplateWithMisplacedPlaceholders_ReturnsUnchangedTemplate()
    {
        $token    = new InitialContents();
        $template = 'example <<<original.content} template {original.content>>> and {some.token} content';

        $this->assertSame($template, $token->replacePlaceholders($template));
    }

    public function testInitialVsReplaced()
    {
        $template = 'example {original.content>>>initial template content<<<original.content} and {some.token}';

        $token    = new InitialContents();
        $expected = 'example initial template content and {some.token}';
        $this->assertSame($expected, $token->replacePlaceholders($template));

        $token    = new InitialContents(false);
        $expected = 'example {original.content} and {some.token}';
        $this->assertSame($expected, $token->replacePlaceholders($template));
    }

    /**
     * @dataProvider replaceWithInitialValue
     * @param string $template
     * @param string $expected
     */
    public function testReplacingWithInitialValue(string $template, string $expected)
    {
        $token = new InitialContents();
        $this->assertSame($expected, $token->replacePlaceholders($template));
    }

    /**
     * @dataProvider replaceWithPlaceholder
     * @param string $template
     * @param string $expected
     */
    public function testReplacingWithOriginalContentPlaceholder(string $template, string $expected)
    {
        $token = new InitialContents(false);
        $this->assertSame($expected, $token->replacePlaceholders($template));
    }

    public function replaceWithPlaceholder(): array
    {
        $examples = [];
        foreach ($this->transformations() as $name => [$template, , $replaced]) {
            $examples[$name] = [$template, $replaced];
        }
        return $examples;
    }

    public function replaceWithInitialValue(): array
    {
        $examples = [];
        foreach ($this->transformations() as $name => [$template, $replaced, ]) {
            $examples[$name] = [$template, $replaced];
        }
        return $examples;
    }

    public function transformations(): array
    {
        $orig      = '{original.content}';
        $wrap      = fn (string $init) => '{original.content>>>' . $init . '<<<original.content}';
        $multiline = <<<'TPL'
            This is multi line content,
            and this is its second line {placeholder?}
            and the next one. Here it ends >>>
            TPL;

        return [
            'single' => [
                'foo bar ' . $wrap('baz') . ' bar foo',
                'foo bar baz bar foo',
                "foo bar $orig bar foo"
            ],
            'double' => [
                'foo bar ' . $wrap('baz') . ' bar-' . $wrap('foo'),
                'foo bar baz bar-foo',
                "foo bar $orig bar-$orig"
            ],
            'multiline token' => [
                'foo bar ' . $wrap($multiline) . ' bar-' . $wrap('foo'),
                'foo bar ' . $multiline . ' bar-foo',
                "foo bar $orig bar-$orig"
            ],
            'inception!' => [
                str_replace('{placeholder?}', $wrap($multiline), $multiline),
                str_replace('{placeholder?}', $multiline, $multiline),
                str_replace('{placeholder?}', $orig, $multiline)
            ]
        ];
    }
}
