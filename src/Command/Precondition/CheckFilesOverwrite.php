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

use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Command\Precondition;


class CheckFilesOverwrite implements Precondition
{
    private Directory $source;
    private Directory $destination;
    private bool      $expected;

    public function __construct(Directory $source, Directory $destination, bool $expected = false)
    {
        $this->source      = $source;
        $this->destination = $destination;
        $this->expected    = $expected;
    }

    public function isFulfilled(): bool
    {
        foreach ($this->source->files() as $sourceFile) {
            $overwrite = $sourceFile->exists() && $this->destination->file($sourceFile->name())->exists();
            if ($overwrite !== $this->expected) { return false; }
        }

        return true;
    }
}
