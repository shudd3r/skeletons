<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Environment\Files;


trait Paths
{
    private function normalized(string $path, string $separator = '/', bool $absolute = false): string
    {
        $replace = array_diff(['\\', '/'], [$separator]);
        $pos     = $absolute && $separator === '\\' ? strpos($path, '://') : false;
        $scheme  = $pos ? substr($path, 0, $pos + 3) : '';
        $path    = str_replace($replace, $separator, $pos ? substr($path, $pos + 3) : $path);

        return $absolute ? $scheme . rtrim($path, $separator) : trim($path, $separator);
    }
}
