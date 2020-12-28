<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Precondition;

use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Command\Precondition;


class CheckFileExists implements Precondition
{
    private File $file;
    private bool $expected;

    public function __construct(File $file, bool $expected = true)
    {
        $this->file     = $file;
        $this->expected = $expected;
    }

    public function isFulfilled(): bool
    {
        return $this->file->exists() === $this->expected;
    }
}
