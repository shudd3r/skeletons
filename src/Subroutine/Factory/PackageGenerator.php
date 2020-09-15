<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Subroutine\Factory;

use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Template;


class PackageGenerator implements Subroutine\Factory
{
    private Directory $skeleton;
    private Directory $package;

    public function __construct(Directory $skeleton, Directory $package)
    {
        $this->skeleton = $skeleton;
        $this->package  = $package;
    }

    public function subroutine(): Subroutine
    {
        $subroutines = [];
        foreach ($this->skeletonFiles($this->skeleton) as $skeletonFile) {
            $subroutines[] = $this->createFor($skeletonFile);
        }

        return new Subroutine\SubroutineSequence(...$subroutines);
    }

    private function skeletonFiles(Directory $directory): array
    {
        $files = $directory->files();
        foreach ($directory->subdirectories() as $subdirectory) {
            $files = array_merge($files, $this->skeletonFiles($subdirectory));
        }

        return $files;
    }

    public function createFor(File $skeletonFile): Subroutine
    {
        return new Subroutine\GenerateFile(
            new Template\FileTemplate($skeletonFile),
            $this->package->file($skeletonFile->pathRelativeTo($this->skeleton))
        );
    }
}
