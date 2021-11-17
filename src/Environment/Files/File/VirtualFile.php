<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\Files\File;

use Shudd3r\Skeletons\Environment\Files\File;
use Shudd3r\Skeletons\Environment\Files\Directory\VirtualDirectory;
use Shudd3r\Skeletons\Environment\Files\Paths;


class VirtualFile implements File
{
    use Paths;

    private ?string $contents;
    private string  $name;

    private VirtualDirectory $root;

    public function __construct(?string $contents = '', string $name = 'file.txt', VirtualDirectory $root = null)
    {
        $this->name     = $this->normalized($name);
        $this->root     = $root ?? new VirtualDirectory();
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

    public function remove(): void
    {
        $this->contents = null;
        $this->root->removeFile($this->name);
    }
}
