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

use Shudd3r\PackageFiles\Application\Command;


class FakeCommand implements Command
{
    private $procedure;

    public function __construct(?callable $procedure = null)
    {
        $this->procedure = $procedure;
    }

    public function execute(): void
    {
        if (isset($this->procedure)) { ($this->procedure)(); }
    }
}
