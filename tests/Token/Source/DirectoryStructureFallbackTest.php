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

use Shudd3r\PackageFiles\Tests\Token\SourceTestCase;
use Shudd3r\PackageFiles\Token\Source\DirectoryStructureFallback;
use Shudd3r\PackageFiles\Tests\Doubles;


class DirectoryStructureFallbackTest extends SourceTestCase
{
    public function testPropertiesResolvedFromDirectoryPath()
    {
        $source       = $this->fakeSource();
        $packageFiles = new Doubles\FakeDirectory(true, 'some/directory/path');
        $fallback     = new DirectoryStructureFallback($source, $packageFiles);

        $expected = new Doubles\FakeSource([
            'repositoryUrl'      => 'https://github.com/directory/path.git',
            'packageName'        => 'directory/path',
            'packageDescription' => 'directory/path package',
            'sourceNamespace'    => 'Directory\\Path'
        ]);
        $this->assertSameSourceValues($expected, $fallback);
    }

    public function testPropertiesResolvedFromPackageName()
    {
        $source   = $this->fakeSource(['packageName' => 'foo/bar', 'packageDescription' => 'Random desc']);
        $fallback = new DirectoryStructureFallback($source, new Doubles\FakeDirectory());

        $expected = new Doubles\FakeSource([
            'repositoryUrl'      => 'https://github.com/foo/bar.git',
            'packageName'        => 'foo/bar',
            'packageDescription' => 'Random desc',
            'sourceNamespace'    => 'Foo\\Bar'
        ]);
        $this->assertSameSourceValues($expected, $fallback);
    }

    private function fakeSource(array $properties = []): Doubles\FakeSource
    {
        $empty = ['repositoryUrl' => '', 'packageName' => '', 'packageDescription' => '', 'sourceNamespace' => ''];
        return new Doubles\FakeSource($properties + $empty);
    }
}
