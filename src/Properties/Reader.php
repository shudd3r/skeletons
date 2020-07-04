<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Properties;

use Shudd3r\PackageFiles\Properties;


class Reader
{
    private Properties $properties;

    public function __construct(Properties $properties)
    {
        $this->properties = $properties;
    }

    public function properties(): Properties
    {
        return new Properties\PackageProperties(
            $this->properties->repositoryName(),
            $this->properties->packageName(),
            $this->properties->packageDescription(),
            $this->properties->sourceNamespace()
        );
    }
}
