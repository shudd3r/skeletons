<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultNamespaceTest extends TestCase
{
    /**
     * @dataProvider valueExamples
     *
     * @param string $invalid
     * @param string $valid
     */
    public function testInvalidReaderValue_ReturnsNull(string $invalid, string $valid)
    {
        $this->assertInstanceOf(Token::class, $this->reader()->create($valid));
        $this->assertNull($this->reader()->create($invalid));
    }

    public function valueExamples()
    {
        return [
            ['Foo/Bar', 'Foo\Bar'],
            ['_Foo\1Bar\Baz', '_Foo\_1Bar\Baz'],
            ['Package:000\na_Me', 'Package000\na_Me']
        ];
    }

    public function testValue_ReturnsComposerNamespace()
    {
        $this->assertSame('Composer\\Namespace', $this->reader()->value());
    }

    public function testMissingComposerNamespace_Value_ReturnsNamespaceFromPackageReader()
    {
        $this->assertSame('Package\\Name', $this->reader(false)->value());
    }

    private function reader(bool $composerData = true): Token\Reader\Source\DefaultNamespace
    {
        $contents = json_encode($composerData ? ['autoload' => ['psr-4' => ['Composer\\Namespace' => 'src/']]] : []);
        $composer = new Token\Reader\Data\ComposerJsonData(new Doubles\MockedFile($contents));
        $fallback = new Token\Reader\PackageReader(new Doubles\FakeSource('package/name'), new Doubles\MockedTerminal());

        return new Token\Reader\Source\DefaultNamespace($composer, $fallback);
    }
}
