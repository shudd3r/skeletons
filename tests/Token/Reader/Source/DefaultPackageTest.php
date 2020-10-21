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
use Shudd3r\PackageFiles\Token\Reader\Source\DefaultPackage;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultPackageTest extends TestCase
{
    public function testValue_ReturnsComposeName()
    {
        $this->assertSame('composer/package', $this->reader()->value());
    }

    public function testMissingComposerNamespace_Value_ReturnsNamespaceFromPackageReader()
    {
        $this->assertSame('directory/package', $this->reader(false)->value());
    }

    private function reader(bool $composer = true): DefaultPackage
    {
        $contents  = json_encode($composer ? ['name' => 'composer/package'] : []);
        $composer  = Doubles\MockedFile::withContents($contents);
        $directory = new Doubles\FakeDirectory(true, '/foo/bar/directory/package');

        return new DefaultPackage(new ComposerJsonData($composer), $directory);
    }
}
