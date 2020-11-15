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
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token\Reader\Source\PackageDescription;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageDescriptionTest extends TestCase
{
    public function testValue_ReturnsComposerDescription()
    {
        $this->assertSame('composer package description', $this->reader()->value());
    }

    public function testMissingComposerDescription_Value_ReturnsDescriptionFromGivenSource()
    {
        $this->assertSame('package/name package', $this->reader(false)->value());
    }

    private function reader(bool $composerData = true)
    {
        $contents = json_encode($composerData ? ['description' => 'composer package description'] : []);
        $composer = new ComposerJsonData(new Doubles\MockedFile($contents));
        return new PackageDescription($composer, new Doubles\FakeSource('package/name'));
    }
}
