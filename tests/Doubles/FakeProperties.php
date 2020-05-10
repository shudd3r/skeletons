<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Properties;


class FakeProperties extends Properties
{
    private array $properties;

    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
    }

    public function repositoryUrl(): string
    {
        return $this->properties['repositoryUrl'] ??= 'https://github.com/polymorphine/dev.git';
    }

    public function packageName(): string
    {
        return $this->properties['packageName'] ??= 'polymorphine/dev';
    }

    public function packageDescription(): string
    {
        return $this->properties['packageDescription'] ??= 'Package description';
    }

    public function sourceNamespace(): string
    {
        return $this->properties['sourceNamespace'] ??= 'Polymorphine\Dev';
    }
}
