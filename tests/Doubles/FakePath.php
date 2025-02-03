<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Environment\Files\Paths;


class FakePath
{
    use Paths { normalized as normalizedPath; }

    private string $path;
    private string $separator;
    private bool   $absolute;

    public function __construct(string $path, string $separator = '/', bool $absolute = false)
    {
        $this->path      = $path;
        $this->separator = $separator;
        $this->absolute  = $absolute;
    }

    public function normalized(): string
    {
        return $this->normalizedPath($this->path, $this->separator, $this->absolute);
    }
}
