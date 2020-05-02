<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Files;

use Shudd3r\PackageFiles\Application\FileSystem\Directory as DirectoryInterface;
use Shudd3r\PackageFiles\Application\FileSystem\File as FileInterface;


class Directory implements DirectoryInterface
{
    private string $path;

    /**
     * @param string $path absolute directory path
     *
     * @throws Exception\InvalidDirectoryException
     */
    public function __construct(string $path)
    {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!$this->exists()) {
            $message = "Cannot reach provided root directory `{$this->path}`";
            throw new Exception\InvalidDirectoryException($message);
        }
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return is_dir($this->path);
    }

    public function file(string $filename): FileInterface
    {
        return new File($this->path . $filename);
    }
}
