<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands\Precondition;

use Shudd3r\PackageFiles\Commands\Precondition;
use Shudd3r\PackageFiles\Replacements\Reader;


class ValidReplacements implements Precondition
{
    private Reader $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function isFulfilled(): bool
    {
        return $this->reader->token() !== null;
    }
}
