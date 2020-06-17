<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Properties;

use Shudd3r\PackageFiles\Tests\PropertiesTestCase;
use Shudd3r\PackageFiles\Properties\InputProperties;
use Shudd3r\PackageFiles\Tests\Doubles;


class InputPropertiesTest extends PropertiesTestCase
{
    public function testWithoutInputDefaultValuesAreUsed()
    {
        $default    = new Doubles\FakeProperties();
        $properties = new InputProperties(new Doubles\MockedTerminal(), $default);

        $this->assertSamePropertyValues($default, $properties);
    }

    public function testInputValuesAreSet()
    {
        $default    = new Doubles\FakeProperties();
        $terminal   = new Doubles\MockedTerminal();
        $properties = new InputProperties($terminal, $default);

        $inputProperties = [
            'repositoryName'     => 'input/package',
            'packageName'        => 'input-package/name',
            'packageDescription' => 'Input package description',
            'sourceNamespace'    => 'Input\Namespace'
        ];

        $terminal->inputStrings = array_values($inputProperties);

        $this->assertSamePropertyValues(new Doubles\FakeProperties($inputProperties), $properties);
    }
}
