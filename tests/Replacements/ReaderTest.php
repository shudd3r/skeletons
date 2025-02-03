<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Replacements;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Environment;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class ReaderTest extends TestCase
{
    public function test_tokens_method_returns_tokens_from_replacements(): void
    {
        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('foo-value'),
            'bar' => new Doubles\FakeReplacement('invalid'),
            'baz' => new Doubles\FakeReplacement('baz-value'),
        ]);

        $expected = ['foo' => 'foo-value', 'bar' => null, 'baz' => 'baz-value'];
        $this->assertTokens($expected, $this->reader()->tokens($replacements));
    }

    public function test_source_fallback_method(): void
    {
        $reader = $this->reader();

        $replacements = new Replacements(['foo' => new Doubles\FakeReplacement('foo-value')]);
        $reader->tokens($replacements);

        $this->assertSame('foo-value', $reader->tokenValueOf('foo'));
        $this->assertSame('', $reader->tokenValueOf('bar'));
    }

    public function test_using_source_fallback_while_reading_tokens(): void
    {
        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('bar', true),
            'bar' => new Doubles\FakeReplacement('bar value'),
            'baz' => new Doubles\FakeReplacement('foo', true)
        ]);

        $expected = ['foo' => 'bar value', 'bar' => 'bar value', 'baz' => 'bar value'];
        $this->assertTokens($expected, $this->reader()->tokens($replacements));
    }

    public function test_fallback_value_for_circular_reference_while_reading_tokens_returns_empty_string(): void
    {
        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('baz', true),
            'bar' => new Doubles\FakeReplacement('bar value'),
            'baz' => new Doubles\FakeReplacement('foo', true)
        ]);

        $expected = ['foo' => '', 'bar' => 'bar value', 'baz' => ''];
        $this->assertTokens($expected, $this->reader()->tokens($replacements));
    }

    public function test_source_data_methods(): void
    {
        $path   = DIRECTORY_SEPARATOR === '/' ? '/path/to/package/directory' : '\\path\\to\\package\\directory';
        $env    = new Doubles\FakeRuntimeEnv(new Environment\Files\Directory\VirtualDirectory($path));
        $reader = $this->reader($env);

        $env->package()->addFile('foo.file', 'foo-file-contents');
        $env->metaData()->save(['foo' => 'foo-meta-value']);
        $env->package()->addFile('composer.json');

        $this->assertSame('foo-file-contents', $reader->fileContents('foo.file'));
        $this->assertSame('', $reader->fileContents('not.file'));
        $this->assertSame($env->composer(), $reader->composer());
        $this->assertSame($path, $reader->packagePath());
        $this->assertSame('foo-meta-value', $reader->metaValueOf('foo'));
        $this->assertNull($reader->metaValueOf('bar'));
    }

    public function test_tokens_method_with_invalid_token_in_interactive_mode_returns_at_first_null_value(): void
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, ['script', 'update']);

        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('foo-value'),
            'bar' => new Doubles\FakeReplacement('invalid'),
            'baz' => new Doubles\FakeReplacement('baz-value'),
        ]);

        $expected = ['foo' => 'foo-value', 'bar' => null];
        $this->assertTokens($expected, $reader->tokens($replacements));

        $messages = $env->output()->messagesSent();
        $this->assertSame('Aborting...', trim($messages[0]));
    }

    public function test_inputValue_method_for_non_interactive_mode_returns_null(): void
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $this->assertNull($reader->inputValue('Enter foo'));
        $this->assertEmpty($env->output()->messagesSent());
    }

    public function test_inputValue_method_for_interactive_mode_returns_prompted_user_input(): void
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, ['script', 'update']);

        $env->input()->addInput('input value');

        $this->assertSame('input value',  $reader->inputValue('Enter foo'));
        $this->assertSame(['  > Enter foo:'], $env->output()->messagesSent());
    }

    public function test_inputValue_method_for_reader_without_input_returns_null_without_prompt_display(): void
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, ['script', 'update'], false);

        $this->assertNull($reader->inputValue('Enter foo'));
        $this->assertEmpty($env->output()->messagesSent());
    }

    public function test_commandArgument_method_returns_command_line_argument_value(): void
    {
        $reader = $this->reader(null, ['command', 'update', 'fooArg=foo command line value', 'barArg=', 'bazArg']);

        $this->assertSame('foo command line value', $reader->commandArgument('fooArg'));
        $this->assertSame('', $reader->commandArgument('barArg'));
        $this->assertSame('', $reader->commandArgument('bazArg'));
        $this->assertNull($reader->commandArgument('notArg'));
    }

    public function test_commandArgument_method_for_reader_without_input_returns_null(): void
    {
        $reader = $this->reader(null, ['script', 'update', 'fooArg=foo value'], false);
        $this->assertNull($reader->commandArgument('fooArg'));
    }

    public function test_sendMessage_method_sends_indented_message_to_output(): void
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $message = <<<'MSG'
            Hello world!
            This is second line of the message
            MSG;
        $expected = <<<'MSG'
                Hello world!
                This is second line of the message
            
            MSG;

        $reader->sendMessage($message);
        $this->assertSame([str_replace("\n", PHP_EOL, $expected)], $env->output()->messagesSent());
    }

    public function test_sendMessage_method_for_reader_without_input_has_no_effect(): void
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, null, false);

        $reader->sendMessage('Hello world!');
        $this->assertEmpty($env->output()->messagesSent());
    }

    private function assertTokens(array $expected, array $tokens): void
    {
        $createToken = function (?string &$value, string $name) {
            $value = is_null($value) ? null : new Replacements\Token\BasicToken($name, $value);
        };
        array_walk($expected, $createToken);
        $this->assertEquals($expected, $tokens);
    }

    private function reader(?Doubles\FakeRuntimeEnv $env = null, ?array $args = null, bool $input = true): Reader
    {
        $env ??= new Doubles\FakeRuntimeEnv();
        return new Reader($env, new InputArgs($args ?: ['script', 'command']), $input);
    }
}
