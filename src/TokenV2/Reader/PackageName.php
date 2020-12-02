<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\TokenV2\Reader;

use Shudd3r\PackageFiles\Token\Source\Data\ComposerJsonData;
use Shudd3r\PackageFiles\Application\FileSystem\Directory;
use Shudd3r\PackageFiles\TokenV2\Source;
use Shudd3r\PackageFiles\Token;


class PackageName extends ValueToken
{
    private ComposerJsonData $composer;
    private Directory        $project;

    public function __construct(ComposerJsonData $composer, Directory $project, Source $source)
    {
        $this->composer = $composer;
        $this->project  = $project;
        parent::__construct($source);
    }

    public function isValid(string $value): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $value);
    }

    public function parsedValue(): string
    {
        return $this->composer->value('name') ?? $this->directoryFallback();
    }

    protected function newTokenInstance(string $packageName): Token
    {
        return new Token\CompositeToken(
            new Token\ValueToken('{package.name}', $packageName),
            new Token\ValueToken('{package.title}', $this->titleName($packageName))
        );
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
