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

use Shudd3r\PackageFiles\RuntimeEnv;


class FakeRuntimeEnv extends RuntimeEnv
{
    private MockedTerminal $cli;
    private FakeDirectory  $pkg;
    private FakeDirectory  $tpl;
    private FakeDirectory  $bkp;

    public function __construct() {
        $this->cli = new MockedTerminal();
        $this->pkg = new FakeDirectory();
        $this->tpl = new FakeDirectory();
        $this->bkp = new FakeDirectory();

        parent::__construct($this->cli, $this->cli, $this->pkg, $this->tpl);
    }

    public function input(): MockedTerminal
    {
        return $this->cli;
    }

    public function output(): MockedTerminal
    {
        return $this->cli;
    }

    public function packageDirectory(): FakeDirectory
    {
        return $this->pkg;
    }

    public function skeletonDirectory(): FakeDirectory
    {
        return $this->tpl;
    }

    public function backupDirectory(): FakeDirectory
    {
        return $this->bkp;
    }
}
