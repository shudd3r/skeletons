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

use Shudd3r\PackageFiles\Properties;


class FakeProperties extends Properties
{
    public array $propertiesCalled = [
        'repositoryUrl'      => 0,
        'repositoryName'     => 0,
        'packageName'        => 0,
        'packageDescription' => 0,
        'sourceNamespace'    => 0
    ];

    public array $properties = [
        'repositoryUrl'      => 'https://github.com/polymorphine/dev.git',
        'repositoryName'     => null,
        'packageName'        => 'polymorphine/dev',
        'packageDescription' => 'Package description',
        'sourceNamespace'    => 'Polymorphine\Dev'
    ];

    public function __construct(array $properties = [])
    {
        $this->properties = $properties + $this->properties;
    }

    public function repositoryUrl(): string
    {
        return $this->get('repositoryUrl');
    }

    public function repositoryName(): string
    {
        if (!isset($this->properties['repositoryName'])) {
            $this->properties['repositoryName'] = parent::repositoryName();
        }

        return $this->get('repositoryName');
    }

    public function packageName(): string
    {
        return $this->get('packageName');
    }

    public function packageDescription(): string
    {
        return $this->get('packageDescription');
    }

    public function sourceNamespace(): string
    {
        return $this->get('sourceNamespace');
    }

    private function get(string $key): string
    {
        $this->propertiesCalled[$key]++;
        return $this->properties[$key];
    }
}
