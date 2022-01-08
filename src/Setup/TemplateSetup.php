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

use Shudd3r\Skeletons\Templates\Contents;
use Closure;


class TemplateSetup
{
    private AppSetup $setup;
    private string   $filename;

    public function __construct(AppSetup $setup, string $filename)
    {
        $this->setup    = $setup;
        $this->filename = $filename;
    }

    /**
     * @see Contents
     *
     * @param Closure $createTemplate fn (Contents) => Template
     */
    public function createWith(Closure $createTemplate): void
    {
        $this->setup->addTemplate($this->filename, $createTemplate);
    }
}
