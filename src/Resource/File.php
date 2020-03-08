<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Resource;

use Shudd3r\PackageFiles\Resource;


class File implements Resource
{
    private string $filename;
    private string $contents;

    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function contents(): string
    {
        return $this->contents ?? $this->contents = file_get_contents($this->filename);
    }

    public function write(string $contents): void
    {
        file_put_contents($this->filename, $this->contents = $contents);
    }
}
