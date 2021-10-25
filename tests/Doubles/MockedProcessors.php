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

use Shudd3r\PackageFiles\Processors;
use Shudd3r\PackageFiles\Templates\Template;
use Shudd3r\PackageFiles\Environment\FileSystem\File;


class MockedProcessors implements Processors
{
    private array $usedTemplates = [];

    public function processor(Template $template, File $packageFile): Processors\Processor
    {
        $this->usedTemplates[$packageFile->name()] = $template;
        return new MockedProcessor();
    }

    public function usedTemplates(): array
    {
        return $this->usedTemplates;
    }
}
