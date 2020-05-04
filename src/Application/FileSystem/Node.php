<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\FileSystem;


interface Node
{
    /**
     * @return string absolute path to file
     */
    public function path(): string;

    /**
     * @return bool
     */
    public function exists(): bool;
}