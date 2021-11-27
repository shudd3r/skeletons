<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Rework;

use Shudd3r\Skeletons\Rework\Replacements\Replacement;


class Replacements
{
    /** @var Replacement[] */
    private array $replacements;

    public function __construct(array $replacements)
    {
        $this->replacements = $replacements;
    }

    public function placeholders(): array
    {
        return array_keys($this->replacements);
    }

    public function replacement(string $replacementName): ?Replacement
    {
        return $this->replacements[$replacementName] ?? null;
    }

    public function info(): array
    {
        $describe = fn (string $name, Replacement $replacement): ?string =>  $replacement->description($name) ?: null;
        $argsInfo = array_map($describe, $this->placeholders(), $this->replacements);
        return array_values(array_filter($argsInfo));
    }
}
