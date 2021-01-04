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
use Shudd3r\PackageFiles\Application\Token\Source\DefaultPackageName;
use Shudd3r\PackageFiles\Application\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultPackageNameTest extends TestCase
{
    public function testWithComposerName_ValueMethod_ResolvesNameFromComposer()
    {
        $this->assertSame('composer/package', $this->source()->value(new Doubles\FakeValidator()));
    }

    public function testWithEmptyComposerName_ValueMethod_ResolvesNameFromDirectoryStructure()
    {
        $this->assertSame('root/path', $this->source(false)->value(new Doubles\FakeValidator()));
    }

    private function source(bool $composer = true): DefaultPackageName
    {
        $contents = json_encode($composer ? ['name' => 'composer/package'] : []);
        $composer = new ComposerJsonData(new Doubles\MockedFile($contents));
        $project  = new Doubles\FakeDirectory('root/path');

        return new DefaultPackageName($composer, $project);
    }
}
