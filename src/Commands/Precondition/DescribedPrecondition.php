<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands\Precondition;

use Shudd3r\Skeletons\Commands\Precondition;


class DescribedPrecondition implements Precondition
{
    private Precondition $precondition;
    private Messages     $messages;

    public function __construct(Precondition $precondition, Messages $messages)
    {
        $this->precondition = $precondition;
        $this->messages     = $messages;
    }

    public function isFulfilled(): bool
    {
        $this->messages->describeProcedure();
        $isFulfilled = $this->precondition->isFulfilled();

        $this->messages->sendResult($isFulfilled);
        return $isFulfilled;
    }
}
