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


class Repository
{
    private string $name;

    public function __construct(string $name)
    {
        if (!preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $name)) {
            throw new Exception("Invalid github repository name `{$name}`");
        }
        $this->name = $name;
    }

    public function name(): string
    {
        return $this->name;
    }
}
