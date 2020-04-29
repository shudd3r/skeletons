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

use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\File as FileInterface;


class ProjectFiles implements Directory
{
    private string $rootDirectory;

    /**
     * Operations on files in given root directory.
     *
     * @param string $rootDirectory
     *
     * @throws Exception\InvalidDirectoryException
     */
    public function __construct(string $rootDirectory)
    {
        if (!is_dir($rootDirectory)) {
            $message = "Cannot reach provided root directory `{$rootDirectory}`";
            throw new Exception\InvalidDirectoryException($message);
        }

        $this->rootDirectory = rtrim($rootDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function path(): string
    {
        return $this->rootDirectory;
    }

    public function file(string $filename): FileInterface
    {
        return new File($filename, $this);
    }

    public function save(FileInterface $file): void
    {
        file_put_contents($this->rootDirectory . $file->name(), $file->contents());
    }
}
