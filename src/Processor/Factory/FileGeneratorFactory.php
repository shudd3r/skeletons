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


class FileGeneratorFactory implements Processor\Factory
{
    private Directory $package;

    public function __construct(Directory $package)
    {
        $this->package = $package;
    }

    public function processor(File $skeletonFile): Processor
    {
        $template    = new Template\FileTemplate($skeletonFile);
        $packageFile = $skeletonFile->reflectedIn($this->package);

        return new Processor\GenerateFile($template, $packageFile);
    }
}
