<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Command;

use Shudd3r\Skeletons\Commands\Command;
use Shudd3r\Skeletons\Replacements\Tokens;
use Shudd3r\Skeletons\Replacements\Data\MetaData;


class SaveMetaData implements Command
{
    private Tokens   $tokens;
    private MetaData $metaData;

    public function __construct(Tokens $tokens, MetaData $metaData)
    {
        $this->tokens   = $tokens;
        $this->metaData = $metaData;
    }

    public function execute(): void
    {
        $this->metaData->save($this->tokens->placeholderValues());
    }
}
