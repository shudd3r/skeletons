<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles\Rework;

use Shudd3r\Skeletons\Rework\Replacements\Reader;
use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Tests\Doubles;


class FakeReader extends Reader
{
    public function __construct(?RuntimeEnv $env = null, array $args = [])
    {
        parent::__construct($env ?? new Doubles\FakeRuntimeEnv(), new InputArgs($args));
    }
}
