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


class TemplateSetup
{
    private EnvSetup $setup;
    private string   $filename;

    public function __construct(EnvSetup $setup, string $filename)
    {
        $this->setup    = $setup;
        $this->filename = $filename;
    }

    public function add(callable $replacement): void
    {
        $this->setup->addTemplate($this->filename, $replacement);
    }
}
