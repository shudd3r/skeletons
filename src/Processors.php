<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\FileSystem\File;
use Shudd3r\Skeletons\Processors\Processor;


interface Processors
{
    public function processor(Template $template, File $packageFile): Processor;
}
