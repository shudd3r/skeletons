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

use Exception;


class MainNamespace
{
    private string $namespace;

    public function __construct(string $namespace)
    {
        foreach (explode('\\', $namespace) as $label) {
            if (!preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label)) {
                throw new Exception("Invalid label `{$label}` in `{$namespace}` namespace");
            }
        }

        $this->namespace = $namespace;
    }

    public function src(): string
    {
        return $this->namespace;
    }
}
