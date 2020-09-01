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
use Shudd3r\PackageFiles\Token\Reader\Source\DefaultNamespace;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token\Reader\PackageReader;
use Shudd3r\PackageFiles\Tests\Doubles;


class DefaultNamespaceTest extends TestCase
{
    public function testValue_ReturnsComposerNamespace()
    {
        $this->assertSame('Composer\\Namespace', $this->reader()->value());
    }

    public function testMissingComposerNamespace_Value_ReturnsNamespaceFromPackageReader()
    {
        $this->assertSame('Package\\Name', $this->reader(false)->value());
    }

    private function reader(bool $composer = true): DefaultNamespace
    {
        $composer = $composer ? ['autoload' => ['psr-4' => ['Composer\\Namespace' => 'src/']]] : [];
        $composer = new ComposerJsonData(new Doubles\MockedFile(json_encode($composer)));
        $fallback = new PackageReader(new Doubles\FakeSource('package/name'));

        return new DefaultNamespace($composer, $fallback);
    }
}
