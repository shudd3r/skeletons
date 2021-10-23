<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Replacements;

use Shudd3r\PackageFiles\Replacements\Reader\FallbackReader;
use Shudd3r\PackageFiles\Replacements\Token\ValueToken;
use Shudd3r\PackageFiles\RuntimeEnv;


interface Replacement
{
    public function optionName(): ?string;

    public function inputPrompt(): ?string;

    public function defaultValue(RuntimeEnv $env, FallbackReader $fallback): ?string;

    public function isValid(string $value): bool;

    public function token(string $name, string $value): ?ValueToken;
}
