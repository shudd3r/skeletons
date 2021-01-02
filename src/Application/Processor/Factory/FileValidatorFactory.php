<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor\Factory;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\Output;


class FileValidatorFactory implements Processor\Factory
{
    private Directory $package;
    private Output    $output;

    public function __construct(Directory $package, Output $output)
    {
        $this->package = $package;
        $this->output  = $output;
    }

    public function processor(File $skeletonFile): Processor
    {
        $template    = new Template\FileTemplate($skeletonFile);
        $packageFile = $this->package->file($skeletonFile->name());

        return new Processor\CompareFile($template, $packageFile, $this->output);
    }
}
