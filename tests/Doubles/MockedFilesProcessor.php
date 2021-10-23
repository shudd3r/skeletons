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

use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class MockedFilesProcessor extends Processor\FilesProcessor
{
    private array $usedTemplates = [];

    public function usedTemplates(): array
    {
        return $this->usedTemplates;
    }

    protected function processor(Template $template, File $packageFile): Processor
    {
        $this->usedTemplates[$packageFile->name()] = $template;
        return new MockedProcessor();
    }
}
