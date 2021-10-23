<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands\Command;

use Shudd3r\PackageFiles\Commands\Command;
use Shudd3r\PackageFiles\Replacements\Reader;
use Shudd3r\PackageFiles\Replacements\Data\MetaData;


class SaveMetaData implements Command
{
    private Reader   $reader;
    private MetaData $metaData;

    public function __construct(Reader $reader, MetaData $metaData)
    {
        $this->reader   = $reader;
        $this->metaData = $metaData;
    }

    public function execute(): void
    {
        $this->metaData->save($this->reader->tokenValues());
    }
}