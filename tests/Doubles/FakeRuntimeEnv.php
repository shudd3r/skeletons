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
    public MockedTerminal $terminal;
    public FakeDirectory  $directory;
    public FakeDirectory  $templates;

    public function __construct(
        MockedTerminal $terminal = null,
        FakeDirectory $packageFiles = null,
        FakeDirectory $skeletonFiles = null
    ) {
        $this->terminal  = $terminal ?? new MockedTerminal();
        $this->directory = $packageFiles ?? new FakeDirectory();
        $this->templates = $skeletonFiles ?? new FakeDirectory();

        parent::__construct($this->terminal, $this->terminal, $this->directory, $this->templates);
    }

    public function input(): MockedTerminal
    {
        return $this->terminal;
    }

    public function output(): MockedTerminal
    {
        return $this->terminal;
    }

    public function packageDirectory(): FakeDirectory
    {
        return $this->directory;
    }

    public function skeletonDirectory(): FakeDirectory
    {
        return $this->templates;
    }
}
