<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Precondition;

use Shudd3r\Skeletons\Commands\Precondition;
use Shudd3r\Skeletons\Environment\Files;


class CheckFilesOverwrite implements Precondition
{
    private Files $destination;
    private bool  $expected;

    public function __construct(Files $destination, bool $expected = false)
    {
        $this->destination = $destination;
        $this->expected    = $expected;
    }

    public function isFulfilled(): bool
    {
        foreach ($this->destination->fileList() as $file) {
            if ($file->exists() !== $this->expected) { return false; }
        }

        return true;
    }
}
