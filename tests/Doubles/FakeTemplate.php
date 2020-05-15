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

use Shudd3r\PackageFiles\Template;
use Shudd3r\PackageFiles\Properties;


class FakeTemplate implements Template
{
    public Properties $receivedProperties;

    private string $rendered;

    public function __construct(string $rendered)
    {
        $this->rendered           = $rendered;
        $this->receivedProperties = new FakeProperties();
    }

    public function render(Properties $properties): string
    {
        $this->receivedProperties = $properties;
        return $this->rendered;
    }
}
