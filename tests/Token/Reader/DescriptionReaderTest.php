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
use Shudd3r\PackageFiles\Token\Reader\DescriptionReader;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token\Description;
use Shudd3r\PackageFiles\Tests\Doubles;


class DescriptionReaderTest extends TestCase
{
    public function testTokenCreatedFromFallbackValue()
    {
        $this->assertSame('Fallback package', $this->reader(false)->value());
        $this->assertEquals(new Description('Fallback package'), $this->reader(false)->token());
    }

    public function testTokenCreatedFromComposerValue()
    {
        $this->assertSame('composer description', $this->reader(true)->value());
        $this->assertEquals(new Description('composer description'), $this->reader(true)->token());
    }

    public function testTokenCreatedFromParameterValue()
    {
        $this->assertEquals(new Description('Any value'), $this->reader(false)->createToken('Any value'));
        $this->assertEquals(new Description('Any value'), $this->reader(true)->createToken('Any value'));
    }

    private function reader(bool $composer): DescriptionReader
    {
        $composer = $composer ? ['description' => 'composer description'] : [];
        $composer = new ComposerJsonData(new Doubles\MockedFile(json_encode($composer)));
        $fallback = new Doubles\FakeValueReader('Fallback');

        return new DescriptionReader($composer, $fallback);
    }
}
