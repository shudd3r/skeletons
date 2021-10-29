<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Token\InitialContents;
use Shudd3r\Skeletons\Replacements\Token\OriginalContents;


class InitialContentsTest extends TestCase
{
    public function testForTemplateWithoutPlaceholders_ReturnsUnchangedTemplate()
    {
        $token    = new InitialContents();
        $template = 'example template {some.token} content';

        $this->assertSame($template, $token->replace($template));
    }

    public function testForTemplateWithMisplacedPlaceholders_ReturnsUnchangedTemplate()
    {
        $token    = new InitialContents();
        $template = $this->template('example <<<end} template {start>>> and {some.token} content');

        $this->assertSame($template, $token->replace($template));
    }

    public function testInitialVsReplaced()
    {
        $template = $this->template('example {start>>>initial template content<<<end} and {some.token}');

        $token    = new InitialContents();
        $expected = 'example initial template content and {some.token}';
        $this->assertSame($expected, $token->replace($template));

        $token    = new InitialContents(false);
        $expected = 'example {' . OriginalContents::PLACEHOLDER . '} and {some.token}';
        $this->assertSame($expected, $token->replace($template));
    }

    /**
     * @dataProvider replaceWithInitialValue
     * @param string $template
     * @param string $expected
     */
    public function testReplacingWithInitialValue(string $template, string $expected)
    {
        $token = new InitialContents();
        $this->assertSame($expected, $token->replace($template));
    }

    /**
     * @dataProvider replaceWithPlaceholder
     * @param string $template
     * @param string $expected
     */
    public function testReplacingWithOriginalContentPlaceholder(string $template, string $expected)
    {
        $token = new InitialContents(false);
        $this->assertSame($expected, $token->replace($template));
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
        $orig     = '{' . OriginalContents::PLACEHOLDER . '}';
        $template = fn (string $init) => $this->template('{start>>>' . $init . '<<<end}');
        $multiline = <<<'TPL'
            This is multi line content,
            and this is its second line {placeholder?}
            and the next one. Here it ends >>>
            TPL;

        $utf          = ['ᚻᛖ ᛒᚢᛞᛖ ᚩᚾ', '⠍⠊⠣⠞ ⠙⠁⠧⠑ ⠃⠑', '😁Hello!😥', 'Οὐχὶ ταὐτὰ παρίστατ', 'αίგაიቢያዩት ይስቅა', '🌞'];
        $utfTemplate    = $utf[0] . $template($utf[1]) . $utf[2] . $template($utf[3]) . $utf[4] . $template($utf[5]);
        $utfRender      = implode('', $utf);
        $utfPlaceholder = $utf[0] . $orig . $utf[2] . $orig . $utf[4] . $orig;

        return [
            'single' => [
                'foo bar ' . $template('baz') . ' bar foo',
                'foo bar baz bar foo',
                "foo bar $orig bar foo"
            ],
            'double' => [
                'foo bar ' . $template('baz') . ' bar-' . $template('foo'),
                'foo bar baz bar-foo',
                "foo bar $orig bar-$orig"
            ],
            'multiline token' => [
                'foo bar ' . $template($multiline) . ' bar-' . $template('foo'),
                'foo bar ' . $multiline . ' bar-foo',
                "foo bar $orig bar-$orig"
            ],
            'inception!' => [
                str_replace('{placeholder?}', $template($multiline), $multiline),
                str_replace('{placeholder?}', $multiline, $multiline),
                str_replace('{placeholder?}', $orig, $multiline)
            ],
            'unicode' => [$utfTemplate, $utfRender, $utfPlaceholder]
        ];
    }

    private function template(string $template): string
    {
        $realPlaceholders = [InitialContents::CONTENT_START, InitialContents::CONTENT_END];
        return str_replace(['{start>>>', '<<<end}'], $realPlaceholders, $template);
    }
}
