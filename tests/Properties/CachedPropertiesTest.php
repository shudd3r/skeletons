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
use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Tests\Doubles;


class CachedPropertiesTest extends PropertiesTestCase
{
    public function testPropertiesMemoization()
    {
        $sourceProperties = new Doubles\FakeProperties();
        $cachedProperties = new Properties\CachedProperties($sourceProperties);

        $this->assertSame([0, 0, 0, 0, 0], array_values($sourceProperties->propertiesCalled));

        $this->assertSamePropertyValues($cachedProperties, clone $sourceProperties);
        $this->assertSame([1, 1, 1, 1, 1], array_values($sourceProperties->propertiesCalled));

        $this->assertSamePropertyValues($cachedProperties, clone $sourceProperties);
        $this->assertSame([1, 1, 1, 1, 1], array_values($sourceProperties->propertiesCalled));
    }
}
