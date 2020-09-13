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

use Shudd3r\PackageFiles\Application\FileSystem\Exception\InvalidAncestorDirectory;


interface Node
{
    /**
     * @return string absolute path to node
     */
    public function path(): string;

    /**
     * @param Directory $ancestorDirectory
     *
     * @return string path to node relative to given ancestor directory
     *
     * @throws InvalidAncestorDirectory
     */
    public function pathRelativeTo(Directory $ancestorDirectory): string;

    /**
     * @return bool
     */
    public function exists(): bool;
}
