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
use Shudd3r\PackageFiles\Application\FileSystem\Directory;


class DirectoryStructureFallback implements Source
{
    private Source    $source;
    private Directory $packageFiles;

    public function __construct(Source $source, Directory $packageFiles)
    {
        $this->source       = $source;
        $this->packageFiles = $packageFiles;
    }

    public function repositoryName(): string
    {
        return $this->source->repositoryName() ?: $this->packageName();
    }

    public function packageName(): string
    {
        return $this->source->packageName() ?: $this->packageNameFromDirectory();
    }

    public function packageDescription(): string
    {
        return $this->source->packageDescription() ?: $this->packageName() . ' package';
    }

    public function sourceNamespace(): string
    {
        return $this->source->sourceNamespace() ?: $this->namespaceFromPackageName();
    }

    private function packageNameFromDirectory(): string
    {
        $directory = $this->packageFiles->path();
        return basename(dirname($directory)) . '/' . basename($directory);
    }

    private function namespaceFromPackageName(): string
    {
        [$vendor, $package] = explode('/', $this->packageName());
        return $this->toPascalCase($vendor) . '\\' . $this->toPascalCase($package);
    }

    private function toPascalCase(string $name): string
    {
        $name = ltrim($name, '0..9');
        return implode('', array_map(fn ($part) => ucfirst($part), preg_split('#[_.-]#', $name)));
    }
}
