<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements\Replacement;

use Shudd3r\Skeletons\Replacements\StandardReplacement;
use Shudd3r\Skeletons\Replacements\Source;


class PackageDescription extends StandardReplacement
{
    protected ?string $inputPrompt  = 'Package description';
    protected ?string $argumentName = 'desc';
    protected string  $description  = <<<'DESC'
        Package description [format: non-empty string]
        Replaces {%s} placeholder
        DESC;

    private string $fallbackPlaceholder;

    public function __construct(string $fallbackPlaceholder = '')
    {
        $this->fallbackPlaceholder = $fallbackPlaceholder;
    }

    protected function isValid(string $value): bool
    {
        return !empty($value);
    }

    protected function resolvedValue(Source $source): string
    {
        return $source->composer()->value('description') ??  $this->fallbackDescription($source);
    }

    private function fallbackDescription(Source $source): string
    {
        if (!$this->fallbackPlaceholder) { return ''; }
        $fallbackValue = $source->tokenValueOf($this->fallbackPlaceholder);
        return $fallbackValue ? $fallbackValue . ' package' : '';
    }
}
