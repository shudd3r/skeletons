<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Reader;

use Shudd3r\PackageFiles\Application\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token;


class PackageDescription extends ValueReader
{
    private ComposerJsonData $composer;
    private PackageName      $packageName;

    public function __construct(ComposerJsonData $composer, PackageName $packageName, Source $source = null)
    {
        $this->composer    = $composer;
        $this->packageName = $packageName;
        parent::__construct($source);
    }

    public function isValid(string $value): bool
    {
        return !empty($value);
    }

    public function parsedValue(): string
    {
        return $this->composer->value('description') ?? $this->packageName->value() . ' package';
    }

    protected function newTokenInstance(string $value): Token
    {
        return new Token\ValueToken('{description.text}', $value);
    }
}