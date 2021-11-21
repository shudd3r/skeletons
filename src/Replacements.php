<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons;

use Shudd3r\Skeletons\Replacements\Replacement;
use Shudd3r\Skeletons\Replacements\Reader;


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

    public function info(): array
    {
        $describe = function (Replacement $replacement): ?string {
            if (!$replacement->optionName()) { return null; }
            return str_pad($replacement->optionName(), 10) . $replacement->description();
        };

        return array_filter(array_map($describe, $this->replacements));
    }

    public function tokens(Reader $reader, bool $isInteractive): void
    {
        foreach (array_keys($this->replacements) as $name) {
            $success = $reader->readToken($name);
            if (!$success && $isInteractive) { break; }
        }
    }
}
