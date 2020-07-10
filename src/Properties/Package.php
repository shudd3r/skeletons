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


class Package
{
    private string $name;
    private string $description;

    public function __construct(string $name, string $description)
    {
        if (!preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $name)) {
            throw new Exception("Invalid packagist package name `{$name}`");
        }

        $this->name        = $name;
        $this->description = $description ?: $this->titleName() . ' package';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function titleName(): string
    {
        [$vendor, $package] = explode('/', $this->name);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
