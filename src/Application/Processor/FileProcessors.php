<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Processor;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Template\Templates;


abstract class FileProcessors
{
    private Directory $package;
    private Templates $templates;

    public function __construct(Directory $package, Templates $templates)
    {
        $this->package   = $package;
        $this->templates = $templates;
    }

    public function processor(File $skeletonFile): Processor
    {
        $template    = $this->templates->template($skeletonFile->name());
        $packageFile = $this->package->file($skeletonFile->name());

        return $this->newProcessorInstance($template, $packageFile);
    }

    abstract protected function newProcessorInstance(Template $template, File $packageFile): Processor;
}
