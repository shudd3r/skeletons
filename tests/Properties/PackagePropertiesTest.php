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


class PackagePropertiesTest extends PropertiesTestCase
{
    public function testPropertiesMemoization()
    {
        $sourceProperties = new Doubles\FakeProperties();
        $cachedProperties = new Properties\PackageProperties(
            $sourceProperties->repositoryName(),
            $sourceProperties->repositoryName(),
            $sourceProperties->packageDescription(),
            $sourceProperties->sourceNamespace()
        );

        $this->assertSamePropertyValues(clone $sourceProperties, $cachedProperties);
    }
}
