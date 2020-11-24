<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Token\Source;

use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\Token;


class PackageName implements Source
{
    private ComposerJsonData $composer;
    private Directory        $project;

    public function __construct(ComposerJsonData $composer, Directory $project)
    {
        $this->composer = $composer;
        $this->project  = $project;
    }

    public function create(string $value): ?Token
    {
        $validPackageName = preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
        if (!$validPackageName) { return null; }

        return new Token\CompositeToken(
            new Token\ValueToken('{package.name}', $value),
            new Token\ValueToken('{package.title}', $this->titleName($value))
        );
    }

    public function value(): string
    {
        return $this->composer->value('name') ?? $this->directoryFallback();
    }

    private function directoryFallback(): string
    {
        $path = $this->project->path();
        return $path ? basename(dirname($path)) . '/' . basename($path) : '';
    }

    private function titleName(string $value): string
    {
        [$vendor, $package] = explode('/', $value);
        return ucfirst($vendor) . '/' . ucfirst($package);
    }
}
