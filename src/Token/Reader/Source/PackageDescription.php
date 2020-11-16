<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader\Source;

use Shudd3r\PackageFiles\Token\Reader\Source;
use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token;


class PackageDescription implements Source
{
    private ComposerJsonData $composer;
    private Source           $package;

    public function __construct(ComposerJsonData $composer, Source $package)
    {
        $this->composer = $composer;
        $this->package  = $package;
    }

    public function create(string $value): ?Token
    {
        return $value ? new Token\ValueToken('{description.text}', $value) : null;
    }

    public function value(): string
    {
        return $this->composer->value('description') ?? $this->package->value() . ' package';
    }
}
