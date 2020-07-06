<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Properties\Source;

use Shudd3r\PackageFiles\Tests\Properties\SourceTestCase;
use Shudd3r\PackageFiles\Properties\Source\DirectoryStructureFallback;
use Shudd3r\PackageFiles\Tests\Doubles;


class DirectoryStructureFallbackTest extends SourceTestCase
{
    public function testPropertiesResolvedFromDirectoryPath()
    {
        $sourceProperties = $this->fakeProperties();
        $packageFiles     = new Doubles\FakeDirectory(true, 'some/directory/path');
        $properties       = new DirectoryStructureFallback($sourceProperties, $packageFiles);

        $expected = new Doubles\FakeSource([
            'repositoryUrl'      => 'https://github.com/directory/path.git',
            'packageName'        => 'directory/path',
            'packageDescription' => 'directory/path package',
            'sourceNamespace'    => 'Directory\\Path'
        ]);
        $this->assertSamePropertyValues($expected, $properties);
    }

    public function testPropertiesResolvedFromPackageName()
    {
        $sourceProperties = $this->fakeProperties(['packageName' => 'foo/bar', 'packageDescription' => 'Random desc']);
        $properties       = new DirectoryStructureFallback($sourceProperties, new Doubles\FakeDirectory());

        $expected = new Doubles\FakeSource([
            'repositoryUrl'      => 'https://github.com/foo/bar.git',
            'packageName'        => 'foo/bar',
            'packageDescription' => 'Random desc',
            'sourceNamespace'    => 'Foo\\Bar'
        ]);
        $this->assertSamePropertyValues($expected, $properties);
    }

    private function fakeProperties(array $properties = []): Doubles\FakeSource
    {
        $empty = ['repositoryUrl' => '', 'packageName' => '', 'packageDescription' => '', 'sourceNamespace' => ''];
        return new Doubles\FakeSource($properties + $empty);
    }
}
