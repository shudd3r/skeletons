<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Source;

use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Token\Validator;


class DefaultPackageName implements Source
{
    private ComposerJsonData $composer;
    private Directory        $package;

    public function __construct(ComposerJsonData $composer, Directory $package)
    {
        $this->composer = $composer;
        $this->package  = $package;
    }

    public function value(Validator $validator): string
    {
        return $this->composer->value('name') ?? $this->directoryFallback();
    }

    private function directoryFallback(): string
    {
        $path = $this->package->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }
}