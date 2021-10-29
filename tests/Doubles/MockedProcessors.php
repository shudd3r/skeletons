<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Tests\Doubles;

use Shudd3r\Skeletons\Processors;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\FileSystem\File;


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
