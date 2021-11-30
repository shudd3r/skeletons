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
        return new Doubles\Rework\FakeReader($env, $args ?: ['command', 'update', '-i']);
    }
}
