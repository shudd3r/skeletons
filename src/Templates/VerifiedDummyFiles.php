<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Templates;

use Shudd3r\Skeletons\Environment\Files\File;


class VerifiedDummyFiles
{
    private array $redundantFiles;
    private array $missingFiles;

    public function __construct(array $redundantFiles, array $missingFiles)
    {
        $this->redundantFiles = $redundantFiles;
        $this->missingFiles   = $missingFiles;
    }

    /**
     * @return File[]
     */
    public function redundantFiles(): array
    {
        return $this->redundantFiles;
    }

    /**
     * @return File[]
     */
    public function missingFiles(): array
    {
        return $this->missingFiles;
    }
}
