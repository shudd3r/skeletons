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
use Shudd3r\Skeletons\Rework\Replacements;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class ReaderTest extends TestCase
{
    public function testTokensMethod_ReturnsTokensFromReplacements()
    {
        $reader = $this->reader();

        $replacements = new Replacements([
            'foo' => new Doubles\Rework\FakeReplacement('foo-value'),
            'bar' => new Doubles\Rework\FakeReplacement('bar-value')
        ]);

        $expected = [
            'foo' => new BasicToken('foo', 'foo-value'),
            'bar' => new BasicToken('bar', 'bar-value')
        ];
        $this->assertEquals($expected, $reader->tokens($replacements));
    }

    public function testSourceFallbackMethod()
    {
        $reader = $this->reader();

        $replacements = new Replacements(['foo' => new Doubles\Rework\FakeReplacement('foo-value')]);
        $reader->tokens($replacements);

        $this->assertSame('foo-value', $reader->tokenValueOf('foo'));
        $this->assertSame('', $reader->tokenValueOf('bar'));
    }

    public function testUsingSourceFallbackWhileReadingTokens()
    {
        $reader = $this->reader();

        $replacements = new Replacements([
            'foo' => new Doubles\Rework\FakeReplacement('bar', true),
            'bar' => new Doubles\Rework\FakeReplacement('bar value'),
            'baz' => new Doubles\Rework\FakeReplacement('foo', true)
        ]);

        $expected = [
            'foo' => new BasicToken('foo', 'bar value'),
            'bar' => new BasicToken('bar', 'bar value'),
            'baz' => new BasicToken('baz', 'bar value')
        ];
        $this->assertEquals($expected, $reader->tokens($replacements));
    }

    public function testCircularFallbackReferenceWhileReadingTokens_FallbackValue_ReturnsEmptyString()
    {
        $reader = $this->reader();

        $replacements = new Replacements([
            'foo' => new Doubles\Rework\FakeReplacement('baz', true),
            'bar' => new Doubles\Rework\FakeReplacement('bar value'),
            'baz' => new Doubles\Rework\FakeReplacement('foo', true)
        ]);

        $expected = [
            'foo' => new BasicToken('foo', ''),
            'bar' => new BasicToken('bar', 'bar value'),
            'baz' => new BasicToken('baz', '')
        ];
        $this->assertEquals($expected, $reader->tokens($replacements));
    }

    public function testInputStringMethod_ReturnsValidInputGivenWithinRetryLimit()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $input   = $env->input();
        $isValid = fn (string $value) => $value !== 'invalid';

        $input->addInput('input string');
        $this->assertSame('input string', $reader->inputString('Give value for foo:', $isValid));
        $this->assertSame(['Give value for foo:'], $input->messagesSent());

        $input->reset()->addInput('invalid', 'invalid', 'valid value');
        $this->assertSame('valid value', $reader->inputString('Give value for foo:', $isValid));

        $input->reset()->addInput('invalid', 'invalid', 'invalid', 'valid value');
        $this->assertSame('invalid', $reader->inputString('Give value for foo:', $isValid));
        $messages = $input->messagesSent();
        $this->assertStringContainsString('Invalid value', array_pop($messages));
    }

    public function testSourceDataMethods()
    {
        $env    = new Doubles\FakeRuntimeEnv();
        $reader = $this->reader($env);

        $env->package()->addFile('foo.file', 'foo-file-contents');
        $env->metaData()->save(['foo' => 'foo-meta-value']);
        $env->package()->addFile('composer.json');

        $this->assertSame('foo-file-contents', $reader->fileContents('foo.file'));
        $this->assertSame('', $reader->fileContents('not.file'));
        $this->assertSame($env->composer(), $reader->composer());
        $this->assertSame('foo-meta-value', $reader->metaValueOf('foo'));
        $this->assertNull($reader->metaValueOf('bar'));
    }

    public function testCommandArgument_ReturnsInputArgSourceValue()
    {
        $args   = new InputArgs(['command', 'init', 'fooArg=foo command line value']);
        $reader = $this->reader(null, $args);

        $this->assertSame('foo command line value', $reader->commandArgument('fooArg'));
        $this->assertSame('', $reader->commandArgument('notArg'));
    }

    public function testForNonUserInputCommands_InputSourceValues_ReturnEmptyString()
    {
        $env  = new Doubles\FakeRuntimeEnv();
        $args = new InputArgs(['script', 'command', '-i', 'fooArg=foo value']);
        $reader = $this->reader($env, $args);

        $env->input()->addInput('input string');
        $isValid = fn (string $value) => $value !== 'invalid';

        $this->assertSame('', $reader->inputString('Input prompt:', $isValid));
        $this->assertSame('', $reader->commandArgument('fooArg'));
    }

    private function reader(?RuntimeEnv $env = null, InputArgs $args = null): Replacements\Reader
    {
        return new Replacements\Reader(
            $env ?? new Doubles\FakeRuntimeEnv(),
            $args ?? new InputArgs(['command', 'update', '-i'])
        );
    }
}
