<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reworked;

use Shudd3r\PackageFiles\Replacement;


class Replacements
{
    /** @var Replacement[] */
    private array $replacements;

    public function __construct(array $replacements)
    {
        $this->replacements = $replacements;
    }

    public function replacement(string $replacementName): ?Replacement
    {
        return $this->replacements[$replacementName] ?? null;
    }

    public function tokens(Reader $reader): void
    {
        foreach ($this->replacements as $name => $replacement) {
            $reader->readToken($name, $replacement);
        }
    }
}
