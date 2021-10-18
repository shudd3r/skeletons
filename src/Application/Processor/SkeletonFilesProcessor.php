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
use Shudd3r\PackageFiles\Application\Token;


class SkeletonFilesProcessor implements Processor
{
    private Directory      $skeleton;
    private FileProcessors $processors;

    public function __construct(Directory $generatedFiles, FileProcessors $processors)
    {
        $this->processors = $processors;
        $this->skeleton   = $generatedFiles;
    }

    public function process(Token $token): bool
    {
        $status = true;
        foreach ($this->skeleton->files() as $file) {
            $status = $this->processors->processor($file)->process($token) && $status;
        }

        return $status;
    }
}
