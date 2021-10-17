<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Setup;

use Shudd3r\PackageFiles\Application\Template\Factory;


class TemplateSetup
{
    private AppSetup $setup;
    private string   $filename;

    public function __construct(AppSetup $setup, string $filename)
    {
        $this->setup    = $setup;
        $this->filename = $filename;
    }

    public function add(Factory $template): void
    {
        $this->setup->addTemplate($this->filename, $template);
    }
}
