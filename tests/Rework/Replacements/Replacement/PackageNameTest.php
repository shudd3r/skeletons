<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Rework\Replacements\Replacement;

use PHPUnit\Framework\TestCase;
use Shudd3r\Skeletons\Rework\Replacements\Replacement\PackageName;
use Shudd3r\Skeletons\Rework\Replacements\Source;
use Shudd3r\Skeletons\Rework\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Token;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Tests\Doubles;


class PackageNameTest extends TestCase
{
    public function testWithoutEnoughDataToResolveValue_TokenMethod_ReturnsNull()
    {
        $replacement = new PackageName();
        $source      = $this->source();
        $this->assertNull($replacement->token('foo', $source));
    }

    public function testForPackageDirectoryWithParentDirectory_TokenValueIsResolvedFromPackageDirectoryPath()
    {
        $replacement = new PackageName();
        $package     = new Files\Directory\VirtualDirectory('path/to/directory/package');
        $env         = new Doubles\FakeRuntimeEnv($package);
        $source      = $this->source($env);
        $this->assertToken('directory/package', $replacement->token('foo', $source));
    }

    public function testWithPackageNameInComposerJsonFile_TokenValueIsResolvedWithComposerJsonData()
    {
        $replacement = new PackageName();
        $package     = new Files\Directory\VirtualDirectory('path/to/directory/package');
        $env         = new Doubles\FakeRuntimeEnv($package);
        $source      = $this->source($env);
        $env->package()->addFile('composer.json', '{"name": "composer/package-name"}');
        $this->assertToken('composer/package-name', $replacement->token('foo', $source));
    }

    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testResolvedTokenValue_IsValidated(string $invalid, string $valid)
    {
        $replacement = new PackageName();
        $env         = new Doubles\FakeRuntimeEnv();
        $source      = $this->source($env);
        $env->metaData()->save(['foo' => $valid, 'bar' => $invalid]);
        $this->assertToken($valid, $replacement->token('foo', $source));
        $this->assertNull($replacement->token('bar', $source));
    }

    public function valueExamples(): array
    {
        return [
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    private function assertToken(string $value, Token $token): void
    {
        [$vendor, $package] = explode('/', $value);
        $expected = Token\CompositeToken::withValueToken(
            new Token\BasicToken('foo', $value),
            new Token\BasicToken('foo.title', ucfirst($vendor) . '/' . ucfirst($package))
        );

        $this->assertEquals($expected, $token);
    }

    private function source(?RuntimeEnv $env = null): Source
    {
        return new Reader(
            $env ?? new Doubles\FakeRuntimeEnv(new Files\Directory\VirtualDirectory('/foo')),
            new InputArgs(['script', 'init', '-i'])
        );
    }
}
