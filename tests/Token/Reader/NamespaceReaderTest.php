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
use Shudd3r\PackageFiles\Token\Reader\NamespaceReader;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token\MainNamespace;
use Shudd3r\PackageFiles\Tests\Doubles;


class NamespaceReaderTest extends TestCase
{
    public function testTokenCreatedFromFallbackValue()
    {
        $this->assertSame('Fallback\Namespace', $this->reader(false)->value());
        $this->assertEquals(new MainNamespace('Fallback\Namespace'), $this->reader(false)->token());
    }

    public function testTokenCreatedFromComposerValue()
    {
        $this->assertSame('Composer\Namespace', $this->reader(true)->value());
        $this->assertEquals(new MainNamespace('Composer\Namespace'), $this->reader(true)->token());
    }

    public function testTokenCreatedFromParameterValue()
    {
        $this->assertEquals(new MainNamespace('Some\Name'), $this->reader(false)->createToken('Some\Name'));
        $this->assertEquals(new MainNamespace('Some\Name'), $this->reader(true)->createToken('Some\Name'));
    }

    private function reader(bool $composer): NamespaceReader
    {
        $composer = $composer ? ['autoload' => ['psr-4' => ['Composer\\Namespace' => 'src/']]] : [];
        $composer = new ComposerJsonData(new Doubles\MockedFile(json_encode($composer)));
        $fallback = new Doubles\FakeValueReader('fallback/namespace');

        return new NamespaceReader($composer, $fallback);
    }
}
