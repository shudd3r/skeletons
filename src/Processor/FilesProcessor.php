<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Processor;

use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Templates;
use Shudd3r\PackageFiles\Replacements\Token;


class FilesProcessor implements Processor
{
    private Directory  $generated;
    private Templates  $templates;
    private Processors $processors;

    public function __construct(Directory $generatedFiles, Templates $templates, Processors $processors)
    {
        $this->templates  = $templates;
        $this->generated  = $generatedFiles;
        $this->processors = $processors;
    }

    public function process(Token $token): bool
    {
        $status = true;
        foreach ($this->generated->files() as $packageFile) {
            $template  = $this->templates->template($packageFile->name());
            $processor = $this->processors->processor($template, $packageFile);
            $status    = $processor->process($token) && $status;
        }

        return $status;
    }
}
