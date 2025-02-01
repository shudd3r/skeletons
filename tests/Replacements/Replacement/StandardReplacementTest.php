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
use Shudd3r\Skeletons\Tests\Doubles\FakeReplacement as Replacement;
use Shudd3r\Skeletons\Tests\Doubles\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;


class StandardReplacementTest extends TestCase
{
    public function test_without_defined_input_source_and_meta_data_Token_value_is_resolved(): void
    {
        $this->assertToken('resolved value', $this->replacement(), Source::create());
    }

    public function test_value_is_resolved_with_defined_meta_data(): void
    {
        $source = Source::create(['foo' => 'meta value']);
        $this->assertToken('meta value', $this->replacement(), $source);
    }

    public function test_value_is_resolved_from_command_line_argument(): void
    {
        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'arg value']);
        $this->assertToken('arg value', $this->replacement()->withInputArg('fooArg'), $source);

        $source = Source::create(['foo' => 'meta value'], ['emptyArg' => '']);
        $this->assertToken('', $this->replacement()->withInputArg('emptyArg'), $source);
    }

    public function test_for_invalid_command_line_value_token_method_returns_null(): void
    {
        $replacement = $this->replacement()->withInputArg('fooArg');

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid']);
        $this->assertNull($replacement->token('foo', $source), 'Non-input type Replacement');

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid', 'i' => false]);
        $this->assertNull($replacement->withPrompt('Enter foo')->token('foo', $source), 'Non-interactive mode');
    }

    public function test_with_input_prompt_property_value_can_be_resolved_from_interactive_input(): void
    {
        $source = Source::create()->withInputStrings('input value');
        $this->assertToken('input value', $this->replacement('default value')->withPrompt('Enter foo'), $source);
        $this->assertSame('Enter foo [default: default value]', $source->promptUsed());
    }

    public function test_for_empty_input_value_is_resolved_to_default(): void
    {
        $replacement = $this->replacement()->withPrompt('Enter foo');

        $source = Source::create();
        $this->assertToken('resolved value', $replacement, $source);
        $this->assertSame('Enter foo [default: resolved value]', $source->promptUsed());

        $source = Source::create(['foo' => 'meta value']);
        $this->assertToken('meta value', $replacement, $source);
        $this->assertSame('Enter foo [default: meta value]', $source->promptUsed());

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'arg value']);
        $this->assertToken('arg value', $replacement->withInputArg('fooArg'), $source);
        $this->assertSame('Enter foo [default: arg value]', $source->promptUsed());
    }

    public function test_empty_default_value_is_not_displayed_in_input_prompt(): void
    {
        $replacement = $this->replacement('')->withPrompt('Enter foo');

        $source = Source::create()->withInputStrings('input value');
        $this->assertToken('input value', $replacement, $source);
        $this->assertSame('Enter foo', $source->promptUsed());
    }

    public function test_invalid_value_cannot_be_default_for_interactive_input(): void
    {
        $replacement = $this->replacement('invalid')->withPrompt('Enter foo');

        $source = Source::create();
        $this->assertToken('', $replacement, $source);
        $this->assertSame('Enter foo', $source->promptUsed());

        $source = Source::create(['foo' => 'invalid']);
        $this->assertToken('', $replacement, $source);
        $this->assertSame('Enter foo', $source->promptUsed());

        $source = Source::create(['foo' => 'invalid'], ['fooArg' => 'invalid']);
        $this->assertToken('', $replacement->withInputArg('fooArg'), $source);
        $this->assertSame('Enter foo', $source->promptUsed());
    }

    public function test_invalid_argument_value_is_ignored_for_interactive_input(): void
    {
        $replacement = $this->replacement()->withPrompt('Enter foo')->withInputArg('fooArg');

        $source = Source::create([], ['fooArg' => 'invalid']);
        $this->assertToken('resolved value', $replacement, $source);

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid']);
        $this->assertToken('meta value', $replacement, $source);
    }

    public function test_for_invalid_input_value_token_method_returns_null(): void
    {
        $replacement = $this->replacement('invalid');

        $source = Source::create();
        $this->assertNull($replacement->token('foo', $source));

        $source = Source::create(['foo' => 'invalid'], ['fooArg' => 'invalid'])->withInputStrings('invalid');
        $this->assertNull($replacement->token('foo', $source));
        $this->assertNull($replacement->withInputArg('fooArg')->token('foo', $source));
        $this->assertNull($replacement->withPrompt('Enter foo', 1)->token('foo', $source));

        $expectedMessages = ['Invalid value. Try `help` command for information on this value format.'];
        $this->assertSame($expectedMessages, $source->messagesSent());
    }

    public function test_valid_value_provided_within_retry_limit(): void
    {
        $replacement = $this->replacement();

        $source = Source::create()->withInputStrings('invalid', 'invalid', 'input value');
        $this->assertToken('input value', $replacement->withPrompt('Enter foo', 4), $source);

        $expectedMessages = ['Invalid value. Try again', 'Invalid value. Try again'];
        $this->assertSame($expectedMessages, $source->messagesSent());
    }

    public function test_for_invalid_values_exceeding_retry_limit_token_method_returns_null(): void
    {
        $replacement = $this->replacement();

        $source = Source::create()->withInputStrings('invalid', 'invalid', 'invalid', 'invalid', 'input value');
        $this->assertNull($replacement->withPrompt('Enter foo', 4)->token('foo', $source));

        $expectedMessages = [
            'Invalid value. Try again',
            'Invalid value. Try again',
            'Invalid value. Try once more',
            'Invalid value. Try `help` command for information on this value format.'
        ];
        $this->assertSame($expectedMessages, $source->messagesSent());
    }

    public function test_interactive_input_is_retried_until_valid_value_is_provided(): void
    {
        $replacement = $this->replacement();

        $source = Source::create()->withInputStrings('invalid', 'invalid', 'invalid', 'invalid', 'input value');
        $this->assertToken('input value', $replacement->withPrompt('Enter foo', 0), $source);

        $expectedMessages = array_fill(0, 4, 'Invalid value. Try again');
        $this->assertSame($expectedMessages, $source->messagesSent());
    }

    public function test_without_argument_name_description_returns_empty_string(): void
    {
        $this->assertEmpty($this->replacement()->withDescription('This is Foo')->description('foo'));
    }

    public function test_without_defined_description_property_description_returns_default_placeholder_info(): void
    {
        $this->assertStringContainsString('{foo}', $this->replacement()->withInputArg('fooArg')->description('foo'));
    }

    public function test_description_formatting(): void
    {
        $description = <<<DESC
            This value replaces {%s} placeholder.
            Foo formatting doesn't matter,
            unless its value is literally 'invalid'.
            DESC;

        $replacement = $this->replacement()->withInputArg('fooArg')->withDescription($description);
        $expected    = <<<DESC
              fooArg      This value replaces {foo} placeholder.
                          Foo formatting doesn't matter,
                          unless its value is literally 'invalid'.
            DESC;

        $this->assertSame(str_replace("\n", PHP_EOL, $expected), $replacement->description('foo'));

        $replacement = $replacement->withInputArg('veryLongArgumentName');
        $expected    = <<<DESC
              veryLongArgumentName This value replaces {foo} placeholder.
                          Foo formatting doesn't matter,
                          unless its value is literally 'invalid'.
            DESC;
        $this->assertSame(str_replace("\n", PHP_EOL, $expected), $replacement->description('foo'));
    }

    private function assertToken(string $value, Replacement $replacement, Source $source): void
    {
        $this->assertEquals(new BasicToken('foo', $value), $replacement->token('foo', $source));
    }

    private function replacement(string $value = 'resolved value'): Replacement
    {
        return new Replacement($value);
    }
}
