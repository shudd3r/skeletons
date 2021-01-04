<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Token\Source;

use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token\Reader\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\Token\Reader\PackageName;
use Shudd3r\PackageFiles\Application\Token\Parser;


class DefaultPackageDescription implements Source
{
    private ComposerJsonData $composer;
    private PackageName      $packageName;

    public function __construct(ComposerJsonData $composer, PackageName $packageName)
    {
        $this->composer    = $composer;
        $this->packageName = $packageName;
    }

    public function value(Parser $parser): string
    {
        return $this->composer->value('description') ?? $this->packageName->value() . ' package';
    }
}
