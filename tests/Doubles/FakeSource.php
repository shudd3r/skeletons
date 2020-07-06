<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Properties\Source;


class FakeSource implements Source
{
    public array $properties = [
        'repositoryName'     => 'polymorphine/dev',
        'packageName'        => 'polymorphine/dev',
        'packageDescription' => 'Package description',
        'sourceNamespace'    => 'Polymorphine\Dev'
    ];

    public function __construct(array $properties = [])
    {
        $this->properties = $properties + $this->properties;
    }

    public function repositoryName(): string
    {
        return $this->properties['repositoryName'];
    }

    public function packageName(): string
    {
        return $this->properties['packageName'];
    }

    public function packageDescription(): string
    {
        return $this->properties['packageDescription'];
    }

    public function sourceNamespace(): string
    {
        return $this->properties['sourceNamespace'];
    }
}
