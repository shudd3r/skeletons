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
    private MockedFile     $met;

    public function __construct(FakeDirectory $package = null) {
        $this->pkg = $package ?? new FakeDirectory();
        $this->tpl = new FakeDirectory();
        $this->cli = new MockedTerminal();
        $this->bkp = new FakeDirectory();
        $this->met = new MockedFile(null);

        parent::__construct($this->pkg, $this->tpl, $this->cli, $this->bkp, $this->met);
    }

    public function input(): MockedTerminal
    {
        return $this->cli;
    }

    public function output(): MockedTerminal
    {
        return $this->cli;
    }

    public function package(): FakeDirectory
    {
        return $this->pkg;
    }

    public function skeleton(): FakeDirectory
    {
        return $this->tpl;
    }

    public function backup(): FakeDirectory
    {
        return $this->bkp;
    }

    public function metaDataFile(): MockedFile
    {
        return $this->met;
    }
}
