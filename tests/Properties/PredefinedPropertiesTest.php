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

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Tests\Doubles;


class PredefinedPropertiesTest extends TestCase
{
    public function testNoPredefinedOptionsReturnFromSecondaryProperties()
    {
        $secondary  = new Doubles\FakeProperties();
        $properties = new Properties\PredefinedProperties([], $secondary);

        $this->assertEachProperty($properties, $secondary->properties);
    }

    /**
     * @dataProvider predefinedOption
     *
     * @param array $option
     * @param array $expected
     */
    public function testPredefinedOptions(array $option, array $expected)
    {
        $secondary  = new Doubles\FakeProperties();
        $properties = new Properties\PredefinedProperties($option, $secondary);

        $this->assertEachProperty($properties, $expected + $secondary->properties);
    }

    public function predefinedOption()
    {
        return [
            [['repo' => 'https://github.com/repo/name.git'], ['repositoryUrl' => 'https://github.com/repo/name.git', 'repositoryName' => 'repo/name']],
            [['package' => 'package/name'], ['packageName' => 'package/name']],
            [['desc' => 'Description from options'], ['packageDescription' => 'Description from options']],
            [['ns' => 'Foo\\Bar'], ['sourceNamespace' => 'Foo\\Bar']]
        ];
    }

    private function assertEachProperty(Properties $properties, array $expected)
    {
        $this->assertSame($expected['repositoryUrl'], $properties->repositoryUrl());
        $this->assertSame($expected['repositoryName'], $properties->repositoryName());
        $this->assertSame($expected['packageName'], $properties->packageName());
        $this->assertSame($expected['packageDescription'], $properties->packageDescription());
        $this->assertSame($expected['sourceNamespace'], $properties->sourceNamespace());
    }
}
