<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token;
use Shudd3r\PackageFiles\Tests\Doubles;

class SourceTestCase extends TestCase
{
    protected function assertSameSourceValues(Doubles\FakeSource $expected, Token\Source $source): void
    {
        $this->assertSame($expected->repositoryName(), $source->repositoryName());
        $this->assertSame($expected->packageName(), $source->packageName());
        $this->assertSame($expected->packageDescription(), $source->packageDescription());
        $this->assertSame($expected->sourceNamespace(), $source->sourceNamespace());
    }
}
