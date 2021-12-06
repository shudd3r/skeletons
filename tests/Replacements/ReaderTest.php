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
    use Environment\Files\Paths;

    public function testTokensMethod_ReturnsTokensFromReplacements()
    {
        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('foo-value'),
            'bar' => new Doubles\FakeReplacement('invalid'),
            'baz' => new Doubles\FakeReplacement('baz-value'),
        ]);

        $expected = ['foo' => 'foo-value', 'bar' => null, 'baz' => 'baz-value'];
        $this->assertTokens($expected, $this->reader()->tokens($replacements));
    }

    public function testSourceFallbackMethod()
    {
        $reader = $this->reader();

        $replacements = new Replacements(['foo' => new Doubles\FakeReplacement('foo-value')]);
        $reader->tokens($replacements);

        $this->assertSame('foo-value', $reader->tokenValueOf('foo'));
        $this->assertSame('', $reader->tokenValueOf('bar'));
    }

    public function testUsingSourceFallbackWhileReadingTokens()
    {
        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('bar', true),
            'bar' => new Doubles\FakeReplacement('bar value'),
            'baz' => new Doubles\FakeReplacement('foo', true)
        ]);

        $expected = ['foo' => 'bar value', 'bar' => 'bar value', 'baz' => 'bar value'];
        $this->assertTokens($expected, $this->reader()->tokens($replacements));
    }

    public function testCircularFallbackReferenceWhileReadingTokens_FallbackValue_ReturnsEmptyString()
    {
        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('baz', true),
            'bar' => new Doubles\FakeReplacement('bar value'),
            'baz' => new Doubles\FakeReplacement('foo', true)
        ]);

        $expected = ['foo' => '', 'bar' => 'bar value', 'baz' => ''];
        $this->assertTokens($expected, $this->reader()->tokens($replacements));
    }

    public function testSourceDataMethods()
    {
        $path   = $this->normalized('/path/to/package/directory', DIRECTORY_SEPARATOR, true);
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

    public function testForInvalidTokenInInteractiveMode_TokensMethod_ReturnsAtFirstNullValue()
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

    public function testForNonInteractiveMode_InputValueMethod_ReturnsNull()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $this->assertNull($reader->inputValue('Enter foo'));
        $this->assertEmpty($env->output()->messagesSent());
    }

    public function testForInteractiveMode_InputValueMethod_ReturnsPromptedUserInput()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, ['script', 'update']);

        $env->input()->addInput('input value');

        $this->assertSame('input value',  $reader->inputValue('Enter foo'));
        $this->assertSame(['  > Enter foo:'], $env->output()->messagesSent());
    }

    public function testForNoInputReader_InputValueMethod_ReturnsNullWithoutPromptDisplay()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env, ['script', 'update'], false);

        $this->assertNull($reader->inputValue('Enter foo'));
        $this->assertEmpty($env->output()->messagesSent());
    }

    public function testCommandArgument_ReturnsInputArgSourceValue()
    {
        $reader = $this->reader(null, ['command', 'update', 'fooArg=foo command line value', 'barArg=', 'bazArg']);

        $this->assertSame('foo command line value', $reader->commandArgument('fooArg'));
        $this->assertSame('', $reader->commandArgument('barArg'));
        $this->assertSame('', $reader->commandArgument('bazArg'));
        $this->assertNull($reader->commandArgument('notArg'));
    }

    public function testForNoInputReader_CommandArgumentMethod_ReturnsNull()
    {
        $reader = $this->reader(null, ['script', 'update', 'fooArg=foo value'], false);
        $this->assertNull($reader->commandArgument('fooArg'));
    }

    public function testSendMessageMethod_SendsIndentedMessageToOutput()
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

    public function testForNoInputReader_SendMessageMethod_hasNoEffect()
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

    private function reader(?Doubles\FakeRuntimeEnv $env = null, array $args = null, bool $input = true): Reader
    {
        $env ??= new Doubles\FakeRuntimeEnv();
        return new Reader($env, new InputArgs($args ?: ['script', 'command']), $input);
    }
}
