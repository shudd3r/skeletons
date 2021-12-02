<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Replacements;

use Shudd3r\Skeletons\Replacements\Data\ComposerJsonData;
use Closure;


interface Source
{
    public function commandArgument(string $argumentName): ?string;

    /**
     * @param string       $prompt
     * @param Closure|null $isValid fn (string) => bool
     * @param int          $tries   Number of attempts to provide valid value (0 - unlimited)
     *
     * @return string|null
     */
    public function inputString(string $prompt, Closure $isValid = null, int $tries = 1): ?string;

    public function metaValueOf(string $name): ?string;

    public function composer(): ComposerJsonData;

    public function fileContents(string $filename): string;

    public function packagePath(): string;

    public function tokenValueOf(string $name): string;
}
