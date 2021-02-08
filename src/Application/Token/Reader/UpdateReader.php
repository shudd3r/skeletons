<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reader;

use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Replacement;
use Shudd3r\PackageFiles\Application\Token;


class UpdateReader extends Reader
{
    protected function tokenInstance(string $name, Replacement $replacement): ?Token
    {
        return $replacement->updateToken($name);
    }
}
