<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Processors;

use Shudd3r\Skeletons\Processors;
use Shudd3r\Skeletons\Templates\Template;
use Shudd3r\Skeletons\Environment\Files\File;


class FallbackProcessors implements Processors
{
    private Processors $primary;
    private Processors $fallback;

    public function __construct(Processors $primary, Processors $fallback)
    {
        $this->primary  = $primary;
        $this->fallback = $fallback;
    }

    public function processor(Template $template, File $packageFile): Processor
    {
        $primary  = $this->primary->processor($template, $packageFile);
        $fallback = $this->fallback->processor($template, $packageFile);
        return new Processors\Processor\FallbackProcessor($primary, $fallback);
    }
}
