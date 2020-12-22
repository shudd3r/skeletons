<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command;

use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Exception;


class CheckMetaDataFile implements Command
{
    private File $metaData;
    private bool $expected;

    public function __construct(File $metaData, bool $shouldExist = false)
    {
        $this->metaData = $metaData;
        $this->expected = $shouldExist;
    }

    public function execute(): void
    {
        if ($this->metaData->exists() === $this->expected) { return; }
        throw new Exception('Package skeleton already initialized. Remove MetaData file to re-initialize.');
    }
}
