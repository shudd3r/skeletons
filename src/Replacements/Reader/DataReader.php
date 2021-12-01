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

Use Shudd3r\Skeletons\Replacements\Reader;
use Closure;


class DataReader extends Reader
{
    public function commandArgument(string $argumentName): string
    {
        return '';
    }

    public function inputString(string $prompt, Closure $isValid): string
    {
        return '';
    }
}
