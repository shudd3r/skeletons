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

use Shudd3r\PackageFiles\Replacement;


class ReplacementSetup
{
    private EnvSetup $setup;
    private string   $placeholder;

    public function __construct(EnvSetup $setup, string $placeholder)
    {
        $this->setup       = $setup;
        $this->placeholder = $placeholder;
    }

    public function add(Replacement $replacement): void
    {
        $this->setup->addReplacement($this->placeholder, $replacement);
    }
}
