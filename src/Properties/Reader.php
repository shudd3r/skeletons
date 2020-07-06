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
    private Source $source;

    public function __construct(Source $source)
    {
        $this->source = $source;
    }

    public function properties(): Properties
    {
        return new Properties(
            $this->source->repositoryName(),
            $this->source->packageName(),
            $this->source->packageDescription(),
            $this->source->sourceNamespace()
        );
    }
}
