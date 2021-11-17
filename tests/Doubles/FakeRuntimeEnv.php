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

use Shudd3r\Skeletons\RuntimeEnv;
use Shudd3r\Skeletons\Environment\Files\Directory\VirtualDirectory;
use Shudd3r\Skeletons\Environment\Files\File\VirtualFile;


class FakeRuntimeEnv extends RuntimeEnv
{
    private MockedTerminal   $cli;
    private VirtualDirectory $pkg;
    private VirtualDirectory $tpl;
    private VirtualDirectory $bkp;
    private VirtualFile      $met;

    public function __construct(VirtualDirectory $package = null, VirtualDirectory $templates = null) {
        $this->pkg = $package ?? new VirtualDirectory();
        $this->tpl = $templates ?? new VirtualDirectory();
        $this->cli = new MockedTerminal();
        $this->bkp = new VirtualDirectory();
        $this->met = new VirtualFile(null);

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

    public function package(): VirtualDirectory
    {
        return $this->pkg;
    }

    public function skeleton(): VirtualDirectory
    {
        return $this->tpl;
    }

    public function backup(): VirtualDirectory
    {
        return $this->bkp;
    }

    public function metaDataFile(): VirtualFile
    {
        return $this->met;
    }
}
