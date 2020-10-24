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

use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\File;


class MockedFile implements File
{
    public string    $name;
    public Directory $root;
    public ?string   $contents;

    public function __construct(?string $contents = '')
    {
        $this->name     = 'file.txt';
        $this->root     = new FakeDirectory();
        $this->contents = $contents;
    }

    public function path(): string
    {
        return $this->root->path() . '/' . $this->name;
    }

    public function exists(): bool
    {
        return isset($this->contents);
    }

    public function contents(): string
    {
        return $this->contents ?? '';
    }

    public function write(string $contents): void
    {
        $this->contents = $contents;
        $this->root->files[$this->name] = $this;
    }

    public function reflectedIn(Directory $rootDirectory): File
    {
        if ($rootDirectory->file($this->name)->exists()) {
            return $rootDirectory->file($this->name);
        }

        $file = new self(null);

        $file->name = $this->name;
        $file->root = $rootDirectory;

        return $file;
    }
}
