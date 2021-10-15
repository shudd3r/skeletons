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
use Shudd3r\PackageFiles\Replacement;
use Shudd3r\PackageFiles\Application\Exception;


class AppSetup
{
    private array $replacements = [];

    public function replacements(): Replacements
    {
        return new Replacements($this->replacements);
    }

    public function addReplacement(string $placeholder, Replacement $replacement): void
    {
        if (isset($this->replacements[$placeholder])) {
            throw new Exception\ReplacementOverwriteException();
        }
        $this->replacements[$placeholder] = $replacement;
    }
}
