<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Token\Source;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Token\Source\DefaultPackageDescription;
use Shudd3r\PackageFiles\Application\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultPackageDescriptionTest extends TestCase
{
    public function testWithComposerDescription_ValueMethod_ResolvesDescriptionFromComposerFile()
    {
        $this->assertSame('composer package description', $this->source()->value());
    }

    public function testWithEmptyComposerDescription_ValueMethod_ResolvesDescriptionFromPackageName()
    {
        $this->assertSame('package/name package', $this->source(false)->value());
    }

    private function source(bool $composer = true): DefaultPackageDescription
    {
        $contents = json_encode($composer ? ['description' => 'composer package description'] : []);
        $composer = new ComposerJsonData(new Doubles\MockedFile($contents));
        $package  = new Doubles\FakePackageName('package/name');

        return new DefaultPackageDescription($composer, $package);
    }
}
