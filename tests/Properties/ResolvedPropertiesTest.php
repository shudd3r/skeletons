<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Properties;

use Shudd3r\PackageFiles\Tests\PropertiesTestCase;
use Shudd3r\PackageFiles\Properties\ResolvedProperties;
use Shudd3r\PackageFiles\Files\Directory;
use Shudd3r\PackageFiles\Tests\Doubles;


class ResolvedPropertiesTest extends PropertiesTestCase
{
    public function testPropertiesResolvedFromDirectory()
    {
        $sourceProperties = $this->fakeProperties();
        $properties       = new ResolvedProperties($sourceProperties, new Directory('some/directory/path'));

        $expected = new Doubles\FakeProperties([
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
        $properties       = new ResolvedProperties($sourceProperties, new Directory(''));

        $expected = new Doubles\FakeProperties([
            'repositoryUrl'      => 'https://github.com/foo/bar.git',
            'packageName'        => 'foo/bar',
            'packageDescription' => 'Random desc',
            'sourceNamespace'    => 'Foo\\Bar'
        ]);
        $this->assertSamePropertyValues($expected, $properties);
    }

    private function fakeProperties(array $properties = []): Doubles\FakeProperties
    {
        $empty = ['repositoryUrl' => '', 'packageName' => '', 'packageDescription' => '', 'sourceNamespace' => ''];
        return new Doubles\FakeProperties($properties + $empty);
    }
}
