<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Properties;


class PropertiesTestCase extends TestCase
{
    protected function assertSamePropertyValues(Doubles\FakeProperties $expected, Properties $properties): void
    {
        $this->assertSame($expected->repositoryName(), $properties->repositoryName());
        $this->assertSame($expected->packageName(), $properties->packageName());
        $this->assertSame($expected->packageDescription(), $properties->packageDescription());
        $this->assertSame($expected->sourceNamespace(), $properties->sourceNamespace());
    }
}
