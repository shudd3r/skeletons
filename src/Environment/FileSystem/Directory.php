<?php

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\FileSystem;

use Shudd3r\Skeletons\Environment\Files;


interface Directory extends Files
{
    /**
     * @return string absolute path to directory
     */
    public function path(): string;

    public function exists(): bool;

    /**
     * @param string $name directory name or relative directory path
     *
     * @return Directory
     */
    public function subdirectory(string $name): Directory;

}
