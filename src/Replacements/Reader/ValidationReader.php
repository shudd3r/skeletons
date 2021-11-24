<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Reader;

use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Token;


class ValidationReader extends Reader
{
    protected function readToken(string $name, Replacement $replacement): ?Token
    {
        return $replacement->token($name, $this->metaDataValue($name));
    }
}
