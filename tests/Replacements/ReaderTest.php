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
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Replacements\Token\BasicToken;
use Shudd3r\Skeletons\Environment\Files\Directory;
use Shudd3r\Skeletons\Environment\Files\Paths;
use Shudd3r\Skeletons\Tests\Doubles;


class ReaderTest extends TestCase
{
    use Paths;

    public function testTokensMethod_ReturnsTokensFromReplacements()
    {
        $reader = $this->reader();

        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('foo-value'),
            'bar' => new Doubles\FakeReplacement('invalid'),
            'baz' => new Doubles\FakeReplacement('baz-value'),
        ]);

        $expected = [
            'foo' => new BasicToken('foo', 'foo-value'),
            'bar' => null,
            'baz' => new BasicToken('baz', 'baz-value'),
        ];
        $this->assertEquals($expected, $reader->tokens($replacements));
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
        $reader = $this->reader();

        $replacements = new Replacements([
            'foo' => new Doubles\FakeReplacement('bar', true),
            'bar' => new Doubles\FakeReplacement('bar value'),
            'baz' => new Doubles\FakeReplacement('foo', true)
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
            'foo' => new Doubles\FakeReplacement('baz', true),
            'bar' => new Doubles\FakeReplacement('bar value'),
            'baz' => new Doubles\FakeReplacement('foo', true)
        ]);

        $expected = [
            'foo' => new BasicToken('foo', ''),
            'bar' => new BasicToken('bar', 'bar value'),
            'baz' => new BasicToken('baz', '')
        ];
        $this->assertEquals($expected, $reader->tokens($replacements));
    }

    public function testSourceDataMethods()
    {
        $path   = $this->normalized('/path/to/package/directory', DIRECTORY_SEPARATOR, true);
        $env    = new Doubles\FakeRuntimeEnv(new Directory\VirtualDirectory($path));
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

    private function reader(?Doubles\FakeRuntimeEnv $env = null, array $args = null): Replacements\Reader
    {
        return new Replacements\Reader\DataReader(
            $env ?? new Doubles\FakeRuntimeEnv(),
            new InputArgs($args ?: ['script', 'command'])
        );
    }
}
