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
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Environment\FileSystem\File;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Application\Token;


abstract class FileProcessors implements Processor
{
    private Templates $templates;
    private Directory $generated;

    public function __construct(Directory $generatedFiles, Templates $templates)
    {
        $this->templates = $templates;
        $this->generated = $generatedFiles;
    }

    public function process(Token $token): bool
    {
        $status = true;
        foreach ($this->generated->files() as $packageFile) {
            $status = $this->processor($packageFile)->process($token) && $status;
        }

        return $status;
    }

    private function processor(File $packageFile): Processor
    {
        $template  = $this->templates->template($packageFile->name());
        return $this->newProcessorInstance($template, $packageFile);
    }

    abstract protected function newProcessorInstance(Template $template, File $packageFile): Processor;
}
