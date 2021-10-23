<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacements\Reader;

use Shudd3r\PackageFiles\Replacements\Reader;
use Shudd3r\PackageFiles\Replacements\Replacement;


class ValidationReader extends Reader
{
    public function readToken(string $name, Replacement $replacement): void
    {
        $this->tokens[$name] = $replacement->token($name, $this->metaDataValue($name));
    }
}