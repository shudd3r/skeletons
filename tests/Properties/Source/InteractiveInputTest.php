<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Properties\Source;

use Shudd3r\PackageFiles\Tests\Properties\SourceTestCase;
use Shudd3r\PackageFiles\Properties\Source\InteractiveInput;
use Shudd3r\PackageFiles\Tests\Doubles;


class InteractiveInputTest extends SourceTestCase
{
    public function testWithoutInputDefaultValuesAreUsed()
    {
        $default    = new Doubles\FakeSource();
        $properties = new InteractiveInput(new Doubles\MockedTerminal(), $default);

        $this->assertSamePropertyValues($default, $properties);
    }

    public function testInputValuesAreSet()
    {
        $default    = new Doubles\FakeSource();
        $terminal   = new Doubles\MockedTerminal();
        $properties = new InteractiveInput($terminal, $default);

        $inputProperties = [
            'repositoryName'     => 'input/package',
            'packageName'        => 'input-package/name',
            'packageDescription' => 'Input package description',
            'sourceNamespace'    => 'Input\Namespace'
        ];

        $terminal->inputStrings = array_values($inputProperties);

        $this->assertSamePropertyValues(new Doubles\FakeSource($inputProperties), $properties);
    }
}