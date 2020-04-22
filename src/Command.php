<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;


interface Command
{
    /**
     * Operations on project files.
     *
     * @param Properties $properties
     */
    public function execute(Properties $properties): void;
}