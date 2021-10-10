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

use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\ReplacementReader;
use Shudd3r\PackageFiles\Replacement;


class AppSetup
{
    private array $replacements = [];

    public function replacements(RuntimeEnv $env): Replacements
    {
        $replacements = [];
        foreach ($this->replacements as $name => $replacement) {
            $replacements[$name] = new ReplacementReader($env, $replacement);
        }

        return new Replacements($replacements);
    }

    public function addReplacement(string $placeholder, Replacement $replacement): void
    {
        $this->replacements[$placeholder] = $replacement;
    }
}
