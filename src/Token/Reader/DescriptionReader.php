<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Reader;

use Shudd3r\PackageFiles\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Token;


class DescriptionReader extends ValueReader
{
    protected const PROMPT = 'Package description';
    protected const OPTION = 'desc';

    private ComposerJsonData $composer;
    private ValueReader     $fallback;

    public function __construct(ComposerJsonData $composer, ValueReader $fallback)
    {
        $this->composer = $composer;
        $this->fallback = $fallback;
    }

    public function createToken(string $value): Token
    {
        return new Token\Description($value);
    }

    public function value(): string
    {
        return $this->composer->value('description') ?? $this->fallback->value() . ' package';
    }
}
