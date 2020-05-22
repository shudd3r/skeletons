<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\FileSystem\Directory as DirectoryInterface;
use Shudd3r\PackageFiles\Application\FileSystem\File as FileInterface;


class FakeDirectory implements DirectoryInterface
{
    public string $path;
    public bool   $exists;

    /** @var string[] */
    public array $files = [];

    public function __construct(bool $exists = true, string $path = __DIR__)
    {
        $this->exists = $exists;
        $this->path   = $path;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function file(string $filename): FileInterface
    {
        return new MockedFile(
            $this->files[$filename] ?? '',
            array_key_exists($filename, $this->files),
            $this->path . DIRECTORY_SEPARATOR . $filename
        );
    }
}
