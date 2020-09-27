<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Processor\Factory;

use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Template;


class PackageGenerator implements Processor\Factory
{
    private Directory $skeleton;
    private Directory $package;

    public function __construct(Directory $skeleton, Directory $package)
    {
        $this->skeleton = $skeleton;
        $this->package  = $package;
    }

    public function processor(): Processor
    {
        $processors = [];
        foreach ($this->skeleton->files()->toArray() as $skeletonFile) {
            $processors[] = $this->createFor($skeletonFile);
        }

        return new Processor\ProcessorSequence(...$processors);
    }

    public function createFor(File $skeletonFile): Processor
    {
        return new Processor\GenerateFile(
            new Template\FileTemplate($skeletonFile),
            $this->package->file($skeletonFile->pathRelativeTo($this->skeleton))
        );
    }
}
