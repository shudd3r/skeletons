<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Application\Setup;

use PHPUnit\Framework\TestCase;
use Shudd3r\PackageFiles\Application\Setup\AppSetup;
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Tests\Doubles\FakeReplacement;
use Shudd3r\PackageFiles\Application\Exception;


class AppSetupTest extends TestCase
{
    public function testCreatingReplacementsClassWithGivenReplacementInstances()
    {
        $setup = new AppSetup();
        $setup->addReplacement('foo', new FakeReplacement('foo-value'));
        $setup->addReplacement('bar', new FakeReplacement('bar-value'));

        $expected = new Replacements([
            'foo' => new FakeReplacement('foo-value'),
            'bar' => new FakeReplacement('bar-value'),
        ]);
        $this->assertEquals($expected, $setup->replacements());
    }

    public function testOverwritingDefinedReplacement_ThrowsException()
    {
        $setup = new AppSetup();
        $setup->addReplacement('foo', new FakeReplacement());

        $this->expectException(Exception\ReplacementOverwriteException::class);
        $setup->addReplacement('foo', new FakeReplacement());
    }
}
