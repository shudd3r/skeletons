<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Environment\FileSystem\File;


class MockedFile implements File
{
    private string        $name;
    private FakeDirectory $root;
    private ?string       $contents;

    public function __construct(?string $contents = '', string $name = 'file.txt', FakeDirectory $root = null)
    {
        $this->name     = $name;
        $this->root     = $root ?? new FakeDirectory();
        $this->contents = $contents;
    }

    public function name(): string
    {
        return $this->name;
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
        $this->root->updateIndex($this);
    }
}
