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

use Shudd3r\Skeletons\Commands\Precondition;


class FakePrecondition implements Precondition
{
    private bool $fulfilled;

    public function __construct(bool $fulfilled)
    {
        $this->fulfilled = $fulfilled;
    }

    public function isFulfilled(): bool
    {
        return $this->fulfilled;
    }
}
