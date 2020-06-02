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

use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\Properties;


class MockedSubroutine implements Subroutine
{
    public ?Properties $passedProperties = null;
    private $procedure;

    public function __construct(?callable $procedure = null)
    {
        $this->procedure = $procedure;
    }

    public function process(Properties $properties): void
    {
        $this->passedProperties = $properties;

        if (!isset($this->procedure)) { return; }
        ($this->procedure)();
    }
}
