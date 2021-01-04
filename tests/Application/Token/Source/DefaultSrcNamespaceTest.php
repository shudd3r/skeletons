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
use Shudd3r\PackageFiles\Application\Token\Source\DefaultSrcNamespace;
use Shudd3r\PackageFiles\Application\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Tests\Doubles;

class DefaultSrcNamespaceTest extends TestCase
{
    public function testWithComposerNamespace_ValueMethod_ResolvesNamespaceFromComposerFile()
    {
        $this->assertSame('Composer\\Namespace', $this->source()->value(new Doubles\FakeValidator()));
    }

    public function testWithoutComposerNamespace_ValueMethod_ResolvesNamespaceFromPackageName()
    {
        $this->assertSame('Package\\Name', $this->source(false)->value(new Doubles\FakeValidator()));
    }

    private function source(bool $composer = true): DefaultSrcNamespace
    {
        $contents = json_encode($composer ? ['autoload' => ['psr-4' => ['Composer\\Namespace\\' => 'src/']]] : []);
        $composer = new ComposerJsonData(new Doubles\MockedFile($contents));
        $package  = new Doubles\FakePackageName('package/name');

        return new DefaultSrcNamespace($composer, $package);
    }
}
