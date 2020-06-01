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

    public function __construct(MockedTerminal $terminal, FakeDirectory $packageFiles, FakeDirectory $skeletonFiles)
    {
        $this->terminal  = $terminal;
        $this->directory = $packageFiles;
        $this->templates = $skeletonFiles;

        parent::__construct($terminal, $terminal, $packageFiles, $skeletonFiles);
    }

    public function input(): MockedTerminal
    {
        return $this->terminal;
    }

    public function output(): MockedTerminal
    {
        return $this->terminal;
    }

    public function packageFiles(): FakeDirectory
    {
        return $this->directory;
    }

    public function skeletonFiles(): FakeDirectory
    {
        return $this->templates;
    }
}
