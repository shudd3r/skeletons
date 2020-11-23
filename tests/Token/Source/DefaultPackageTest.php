<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultPackageTest extends TestCase
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
            ['-Packa-ge1/na.me', 'Packa-ge1/na.me'],
            ['1Package000_/na_Me', '1Package000/na_Me']
        ];
    }

    public function testValue_ReturnsComposeName()
    {
        $this->assertSame('composer/package', $this->reader()->value());
    }

    public function testMissingComposerNamespace_Value_ReturnsNamespaceFromPackageReader()
    {
        $this->assertSame('directory/package', $this->reader(false)->value());
    }

    private function reader(bool $composer = true): Token\Source\DefaultPackage
    {
        $contents  = json_encode($composer ? ['name' => 'composer/package'] : []);
        $composer  = new Doubles\MockedFile($contents);
        $directory = new Doubles\FakeDirectory('/foo/bar/directory/package');

        return new Token\Source\DefaultPackage(new Token\Source\Data\ComposerJsonData($composer), $directory);
    }
}
