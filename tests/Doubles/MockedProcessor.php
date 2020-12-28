<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Token;


class MockedProcessor implements Processor
{
    public ?Token $passedToken = null;

    public function __construct()
    {
    }

    public function process(Token $token): void
    {
        $this->passedToken = $token;
    }
}
