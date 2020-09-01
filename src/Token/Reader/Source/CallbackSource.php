<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Token\Reader\Source;


class CallbackSource implements Source
{
    private $callback;

    /**
     * @param callable $callback fn() => string
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function value(): string
    {
        return ($this->callback)();
    }
}
