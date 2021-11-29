<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Rework\Replacements;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Tests\Doubles\Rework\FakeReplacement as Replacement;
use Shudd3r\Skeletons\Tests\Doubles\Rework\FakeSource as Source;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;


class ReplacementTest extends TestCase
{
    public function testWithoutDefinedInputSourceAndMetaValue_Token_ReturnsTokenWithResolvedValue()
    {
        $source = Source::create();
        $this->assertToken('resolved value', $this->replacement(), $source);
    }

    public function testWithMetaValue_Token_ReturnsTokenWithThatValue()
    {
        $source = Source::create(['foo' => 'meta value']);
        $this->assertToken('meta value', $this->replacement(), $source);
    }

    public function testWithArgumentName_Token_ReturnsTokenWithValidInputArgument()
    {
        $replacement = $this->replacement()->withInputArg('fooArg');

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'arg value']);
        $this->assertToken('arg value', $replacement, $source);

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'invalid']);
        $this->assertToken('meta value', $replacement, $source);

        $source = Source::create([], ['fooArg' => 'invalid']);
        $this->assertToken('resolved value', $replacement, $source);
    }

    public function testWithInputPromptProperty_Token_ReturnsTokenUsingInputEntry()
    {
        $source = Source::create()->withInputStrings('input value');
        $this->assertToken('input value', $this->replacement()->withPrompt('Give foo'), $source);
        $this->assertSame('  > Give foo [default: resolved value]:', $source->promptUsed());
    }

    public function testForEmptyInput_Token_ReturnsTokenWithDefaultValue()
    {
        $replacement = $this->replacement()->withPrompt('Give foo');

        $source = Source::create();
        $this->assertToken('resolved value', $replacement, $source);
        $this->assertSame('  > Give foo [default: resolved value]:', $source->promptUsed());

        $source = Source::create(['foo' => 'meta value']);
        $this->assertToken('meta value', $replacement, $source);
        $this->assertSame('  > Give foo [default: meta value]:', $source->promptUsed());

        $source = Source::create(['foo' => 'meta value'], ['fooArg' => 'arg value']);
        $this->assertToken('arg value', $replacement->withInputArg('fooArg'), $source);
        $this->assertSame('  > Give foo [default: arg value]:', $source->promptUsed());
    }

    public function testForInvalidDefaultValueAndEmptyInput_Token_ReturnsTokenWithEmptyString()
    {
        $source = Source::create();
        $this->assertToken('', $this->replacement(false)->withPrompt('Give foo'), $source);
        $this->assertSame('  > Give foo:', $source->promptUsed());
    }

    public function testForInvalidValue_Token_ReturnsNull()
    {
        $source = Source::create();
        $this->assertNull($this->replacement(false)->token('foo', $source));

        $source = Source::create(['foo' => 'invalid']);
        $this->assertNull($this->replacement()->token('foo', $source));

        $source = Source::create()->withInputStrings('invalid');
        $this->assertNull($this->replacement()->withPrompt('Give foo')->token('foo', $source));
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

    private function replacement(bool $valid = true): Replacement
    {
        return new Replacement($valid ? 'resolved value' : 'invalid');
    }
}
