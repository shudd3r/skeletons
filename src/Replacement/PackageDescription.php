<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacement;

use Shudd3r\PackageFiles\Replacement;


class PackageDescription extends Replacement
{
    protected ?string $inputPrompt = 'Package description';
    protected ?string $optionName  = 'desc';

    protected function isValid(string $value): bool
    {
        return !empty($value);
    }

    protected function defaultValue(array $options): string
    {
        return $this->env->composer()->value('description') ?? $this->descriptionFromFallbackValue($options);
    }

    private function descriptionFromFallbackValue(array $options): string
    {
        $package = $this->fallbackValue($options);
        return $package ? $package . ' package' : '';
    }
}
