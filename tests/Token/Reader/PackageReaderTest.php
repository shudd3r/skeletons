<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Reader;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Token\Reader\PackageReader;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token\Package;
use Shudd3r\PackageFiles\Tests\Doubles;


class PackageReaderTest extends TestCase
{
    public function testTokenCreatedFromDirectoryPath()
    {
        $this->assertSame('directory/package', $this->reader(false)->value());
        $this->assertEquals(new Package('directory/package'), $this->reader(false)->token());
    }

    public function testTokenCreatedFromComposerValue()
    {
        $this->assertSame('composer/package', $this->reader(true)->value());
        $this->assertEquals(new Package('composer/package'), $this->reader(true)->token());
    }

    public function testTokenCreatedFromParameterValue()
    {
        $this->assertEquals(new Package('some/package'), $this->reader(false)->createToken('some/package'));
        $this->assertEquals(new Package('some/package'), $this->reader(true)->createToken('some/package'));
    }

    public function testConstantProperties()
    {
        $this->assertSame('Packagist package name', $this->reader(false)->inputPrompt());
        $this->assertSame('package', $this->reader(false)->optionName());
    }

    private function reader(bool $composer): PackageReader
    {
        $composer  = new Doubles\MockedFile(json_encode($composer ? ['name' => 'composer/package'] : []));
        $directory = new Doubles\FakeDirectory(true, '/foo/bar/directory/package');

        return new PackageReader(new ComposerJsonData($composer), $directory);
    }
}
