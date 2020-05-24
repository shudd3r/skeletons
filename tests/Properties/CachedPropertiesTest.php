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


class CachedPropertiesTest extends TestCase
{
    public function testPropertiesMemoization()
    {
        $sourceProperties = new Doubles\FakeProperties();
        $cachedProperties = new Properties\CachedProperties($sourceProperties);

        $this->assertSame([0, 0, 0, 0, 0], array_values($sourceProperties->propertiesCalled));

        $this->callEachProperty($cachedProperties, $sourceProperties->properties);
        $this->assertSame([1, 1, 1, 1, 1], array_values($sourceProperties->propertiesCalled));

        $this->callEachProperty($cachedProperties, $sourceProperties->properties);
        $this->assertSame([1, 1, 1, 1, 1], array_values($sourceProperties->propertiesCalled));
    }

    private function callEachProperty(Properties $properties, array $expected)
    {
        $this->assertSame($expected['repositoryUrl'], $properties->repositoryUrl());
        $this->assertSame($expected['repositoryName'], $properties->repositoryName());
        $this->assertSame($expected['packageName'], $properties->packageName());
        $this->assertSame($expected['packageDescription'], $properties->packageDescription());
        $this->assertSame($expected['sourceNamespace'], $properties->sourceNamespace());
    }
}
