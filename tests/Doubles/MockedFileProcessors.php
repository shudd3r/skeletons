<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Tests\Doubles;

use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class MockedFileProcessors extends Processor\FileProcessors
{
    private ?Template $usedTemplate = null;

    public function usedTemplate(): ?Template
    {
        return $this->usedTemplate;
    }

    protected function newProcessorInstance(Template $template, File $packageFile): Processor
    {
        $this->usedTemplate = $template;
        return new MockedProcessor();
    }
}