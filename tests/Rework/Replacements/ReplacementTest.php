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
use Shudd3r\Skeletons\Rework\Replacements\Replacement;
use Shudd3r\Skeletons\Rework\Replacements\Source;
use Shudd3r\Skeletons\Rework\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class ReplacementTest extends TestCase
{
    public function testWithoutDefinedInputSourceAndMetaValue_Token_ReturnsTokenWithResolvedValue()
    {
        $replacement = $this->replacement();
        $source      = $this->source();
        $this->assertToken('resolved value', $replacement, $source);
    }

    public function testWithMetaValue_Token_ReturnsTokenWithThatValue()
    {
        $replacement = $this->replacement();
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env);
        $env->metaData()->save(['foo' => 'meta value']);
        $this->assertToken('meta value', $replacement, $source);
    }

    public function testWithArgumentName_Token_ReturnsTokenWithValidInputArgument()
    {
        $replacement = $this->replacement()->withInputArg('fooArg');
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env, ['fooArg=arg value']);
        $env->metaData()->save(['foo' => 'meta value']);
        $this->assertToken('arg value', $replacement, $source);

        $source = $this->source($env, ['fooArg=invalid']);
        $this->assertToken('meta value', $replacement, $source);

        $source = $this->source(null, ['fooArg=invalid']);
        $this->assertToken('resolved value', $replacement, $source);
    }

    public function testWithInputPromptProperty_Token_ReturnsTokenUsingInputEntry()
    {
        $replacement = $this->replacement()->withPrompt('Give foo');
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env);
        $env->input()->addInput('input value');
        $this->assertToken('input value', $replacement, $source);
        $this->assertSame(['  > Give foo [default: resolved value]:'], $env->output()->messagesSent());
    }

    public function testForEmptyInput_Token_ReturnsTokenWithDefaultValue()
    {
        $replacement = $this->replacement()->withPrompt('Give foo');
        $source      = $this->source();
        $this->assertToken('resolved value', $replacement, $source);

        $env    = new Doubles\FakeRuntimeEnv();
        $source = $this->source($env);
        $env->metaData()->save(['foo' => 'meta value']);
        $this->assertToken('meta value', $replacement, $source);
        $this->assertSame(['  > Give foo [default: meta value]:'], $env->output()->messagesSent());

        $replacement = $replacement->withInputArg('fooArg');
        $source      = $this->source($env, ['fooArg=arg value']);
        $this->assertToken('arg value', $replacement, $source);
        $this->assertSame(['  > Give foo [default: arg value]:'], $env->output()->messagesSent());
    }

    public function testForInvalidDefaultValueAndEmptyInput_Token_ReturnsTokenWithEmptyString()
    {
        $replacement = $this->replacement(false)->withPrompt('Give foo');
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env);
        $this->assertToken('', $replacement, $source);
        $this->assertSame(['  > Give foo:'], $env->output()->messagesSent());
    }

    public function testForInvalidValue_Token_ReturnsNull()
    {
        $replacement = $this->replacement(false);
        $source      = $this->source();
        $this->assertNull($replacement->token('foo', $source));

        $replacement = $this->replacement();
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env);
        $env->metaData()->save(['foo' => 'invalid']);
        $this->assertNull($replacement->token('foo', $source));

        $replacement = $replacement->withPrompt('Give foo');
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env);
        $env->input()->addInput('invalid', 'invalid', 'invalid');
        $this->assertNull($replacement->token('foo', $source));
    }

    public function testWithoutArgumentName_Description_ReturnsEmptyString()
    {
        $replacement = $this->replacement()->withDescription('This is Foo');
        $this->assertEmpty($replacement->description('foo'));

    }

    public function testWithoutDefinedDescriptionProperty_Description_ReturnsDefaultPlaceholderInfo()
    {
        $replacement = $this->replacement()->withInputArg('fooArg');
        $this->assertStringContainsString('{foo}', $replacement->description('foo'));
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
    }

    private function assertToken(string $value, Replacement $replacement, Source $source): void
    {
        $this->assertEquals(new BasicToken('foo', $value), $replacement->token('foo', $source));
    }

    private function replacement(bool $valid = true): Doubles\Rework\FakeReplacement
    {
        return new Doubles\Rework\FakeReplacement($valid ? 'resolved value' : 'invalid');
    }

    private function source(?RuntimeEnv $env = null, array $args = []): Source
    {
        return new Reader(
            $env ?? new Doubles\FakeRuntimeEnv(),
            new InputArgs(array_merge(['script', 'init', '-i'], $args))
        );
    }
}
