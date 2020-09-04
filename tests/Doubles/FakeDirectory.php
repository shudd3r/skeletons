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

    /** @var MockedFile[] */
    public array $files = [];

    /** @var FakeDirectory[] */
    public array $subdirectories = [];

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

    public function create(): void
    {
        $this->exists = true;
    }

    public function file(string $filename): FileInterface
    {
        return $this->files[$filename] ??= new MockedFile('', false, $this->path . DIRECTORY_SEPARATOR . $filename);
    }

    public function subdirectory(string $name): DirectoryInterface
    {
        return $this->subdirectories[$name] ??= new self(false, $this->path . DIRECTORY_SEPARATOR . $name);
    }

    public function files(): array
    {
        return array_values($this->files);
    }

    public function subdirectories(): array
    {
        return array_values($this->subdirectories);
    }
}
