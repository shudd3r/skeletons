<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Token\Source;

use Shudd3r\PackageFiles\Tests\Token\SourceTestCase;
use Shudd3r\PackageFiles\Token\Source\CommandLineOptions;
use Shudd3r\PackageFiles\Tests\Doubles;


class CommandLineOptionsTest extends SourceTestCase
{
    public function testWithoutPredefinedOptions_ReturnsSecondaryProperties()
    {
        $secondary = new Doubles\FakeSource();
        $source    = new CommandLineOptions([], $secondary);

        $this->assertSameSourceValues($secondary, $source);
    }

    /**
     * @dataProvider predefinedOption
     *
     * @param array $option
     * @param array $expected
     */
    public function testPredefinedOptions(array $option, array $expected)
    {
        $secondary = new Doubles\FakeSource();
        $source    = new CommandLineOptions($option, $secondary);

        $this->assertSameSourceValues(new Doubles\FakeSource($expected + $secondary->properties), $source);
    }

    public function predefinedOption()
    {
        return [
            [['repo' => 'repo/name'], ['repositoryName' => 'repo/name']],
            [['package' => 'package/name'], ['packageName' => 'package/name']],
            [['desc' => 'Description from options'], ['packageDescription' => 'Description from options']],
            [['ns' => 'Foo\\Bar'], ['sourceNamespace' => 'Foo\\Bar']]
        ];
    }
}
