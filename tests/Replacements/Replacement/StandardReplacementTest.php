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
    public function testWithoutDefinedInputSourceAndMetaValue_Token_ReturnsTokenWithResolvedValue()
    {
        $this->assertToken('resolved value', $this->replacement(), Source::create());
    }

    public function testWithMetaValue_Token_ReturnsTokenWithThatValue()
    {
        $source = Source::create(['foo' => 'meta value']);
        $this->assertToken('meta value', $this->replacement(), $source);
    }

    public function testWithArgumentName_Token_ReturnsTokenWithInputArgument()
    {
        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'arg value']);
        $this->assertToken('arg value', $this->replacement()->withInputArg('fooArg'), $source);

        $source = Source::create(['foo' => 'meta value'], ['emptyArg' => '']);
        $this->assertToken('', $this->replacement()->withInputArg('emptyArg'), $source);
    }

    public function testForInvalidArgument_Token_ReturnsNull()
    {
        $replacement = $this->replacement()->withInputArg('fooArg');

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid']);
        $this->assertNull($replacement->token('foo', $source), 'Non-input type Replacement');

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid', 'i' => false]);
        $this->assertNull($replacement->withPrompt('Enter foo')->token('foo', $source), 'Non-interactive mode');
    }

    public function testWithInputPromptProperty_Token_ReturnsTokenUsingInputEntry()
    {
        $source = Source::create()->withInputStrings('input value');
        $this->assertToken('input value', $this->replacement('default value')->withPrompt('Enter foo'), $source);
        $this->assertSame('Enter foo [default: default value]', $source->promptUsed());
    }

    public function testForEmptyInput_Token_ReturnsTokenWithDefaultValue()
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

    public function testForInteractiveInput_EmptyDefaultValue_IsNotDisplayed()
    {
        $replacement = $this->replacement('')->withPrompt('Enter foo');

        $source = Source::create()->withInputStrings('input value');
        $this->assertToken('input value', $replacement, $source);
        $this->assertSame('Enter foo', $source->promptUsed());
    }

    public function testForInteractiveInput_InvalidValueCannotBeDefault()
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

    public function testForInteractiveInput_InvalidArgumentValueIsIgnored()
    {
        $replacement = $this->replacement()->withPrompt('Enter foo')->withInputArg('fooArg');

        $source = Source::create([], ['fooArg' => 'invalid']);
        $this->assertToken('resolved value', $replacement, $source);

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid']);
        $this->assertToken('meta value', $replacement, $source);
    }

    public function testForInvalidValue_Token_ReturnsNull()
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

    public function testForInteractiveInput_ValidValueWithinRetryLimit_ReturnsToken()
    {
        $replacement = $this->replacement();

        $source = Source::create()->withInputStrings('invalid', 'invalid', 'input value');
        $this->assertToken('input value', $replacement->withPrompt('Enter foo', 4), $source);

        $expectedMessages = ['Invalid value. Try again', 'Invalid value. Try again'];
        $this->assertSame($expectedMessages, $source->messagesSent());
    }

    public function testForInteractiveInput_InvalidValuesExceedingRetryLimit_ReturnNull()
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

    public function testForInteractiveInputWithoutRetryLimit_InputIsRepeatedUntilValidValueIsGiven()
    {
        $replacement = $this->replacement();

        $source = Source::create()->withInputStrings('invalid', 'invalid', 'invalid', 'invalid', 'input value');
        $this->assertToken('input value', $replacement->withPrompt('Enter foo', 0), $source);

        $expectedMessages = array_fill(0, 4, 'Invalid value. Try again');
        $this->assertSame($expectedMessages, $source->messagesSent());
    }

    public function testWithoutArgumentName_Description_ReturnsEmptyString()
    {
        $this->assertEmpty($this->replacement()->withDescription('This is Foo')->description('foo'));
    }

    public function testWithoutDefinedDescriptionProperty_Description_ReturnsDefaultPlaceholderInfo()
    {
        $this->assertStringContainsString('{foo}', $this->replacement()->withInputArg('fooArg')->description('foo'));
    }

    public function testDescriptionFormatting()
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
