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


class MockedFile implements FileInterface
{
    public string             $contents;
    public bool               $exists;
    public DirectoryInterface $root;
    public string             $name;

    public function __construct(
        string $contents = '',
        bool $exists = true,
        DirectoryInterface $root = null,
        string $name = 'file.txt'
    ) {
        $this->contents = $contents;
        $this->exists   = $exists;
        $this->root     = $root ?? new FakeDirectory();
        $this->name     = $name;
    }

    public function path(): string
    {
        return $this->root->path() . '/' . $this->name;
    }

    public function exists(): bool
    {
        return $this->exists;
    }

    public function reflectedIn(DirectoryInterface $rootDirectory): self
    {
        $file = new self($this->contents, $this->exists, $rootDirectory, $this->name);

        /** @var $rootDirectory FakeDirectory */
        $rootDirectory->files[$this->name] = $file;
        return $file;
    }

    public function contents(): string
    {
        return $this->exists ? $this->contents : '';
    }

    public function write(string $contents): void
    {
        $this->contents = $contents;
        $this->exists   = true;
    }
}
