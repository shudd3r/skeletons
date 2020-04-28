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

use Shudd3r\PackageFiles\Files;


class ProjectFiles implements Files
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

    public function directory(): string
    {
        return $this->rootDirectory;
    }

    public function exists(string $filename): bool
    {
        return file_exists($this->rootDirectory . $filename);
    }

    public function file(string $filename): File
    {
        return new File($filename, $this);
    }

    public function contents(string $filename): string
    {
        if (!file_exists($this->rootDirectory . $filename)) {
            $message = "File `{$filename}` not found in `{$this->rootDirectory}` directory";
            throw new Exception\FileNotFoundException($message);
        }
        return file_get_contents($this->rootDirectory . $filename);
    }

    public function write(string $filename, string $contents): void
    {
        file_put_contents($this->rootDirectory . $filename, $contents);
    }
}
