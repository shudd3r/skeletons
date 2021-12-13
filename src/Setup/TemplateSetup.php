<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Setup;

use Shudd3r\Skeletons\Templates\Factory;
use Shudd3r\Skeletons\Templates\Contents;


class TemplateSetup
{
    private AppSetup $setup;
    private string   $filename;

    public function __construct(AppSetup $setup, string $filename)
    {
        $this->setup    = $setup;
        $this->filename = $filename;
    }

    public function factory(Factory $factory): void
    {
        $this->setup->addTemplate($this->filename, fn (Contents $contents) => $factory->template($contents));
    }
}
