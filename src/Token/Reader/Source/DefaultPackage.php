<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class DefaultPackage implements Source
{
    private ComposerJsonData $composer;
    private Directory        $project;

    public function __construct(ComposerJsonData $composer, Directory $project)
    {
        $this->composer = $composer;
        $this->project  = $project;
    }

    public function value(): string
    {
        return $this->composer->value('name') ?? $this->directoryFallback();
    }

    private function directoryFallback(): string
    {
        $path = $this->project->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }
}
