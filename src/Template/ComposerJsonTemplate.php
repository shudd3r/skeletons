<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Template;

use Shudd3r\PackageFiles\Template;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Properties;


class ComposerJsonTemplate implements Template
{
    private File $composerFile;

    public function __construct(File $composerFile)
    {
        $this->composerFile = $composerFile;
    }

    public function render(Properties $properties): string
    {
        $composer = json_decode($this->composerFile->contents(), true);

        $namespace   = $properties->sourceNamespace() . '\\';
        $autoload    = $this->normalizedAutoload($composer['autoload'] ?? [], $namespace, 'src/');
        $autoloadDev = $this->normalizedAutoload($composer['autoload-dev'] ?? [], $namespace . 'Tests\\', 'tests/');

        $newComposer = array_filter([
            'name'              => $properties->packageName(),
            'description'       => $properties->packageDescription(),
            'type'              => 'library',
            'license'           => 'MIT',
            'authors'           => $composer['authors'] ?? [['name' => 'Shudd3r', 'email' => 'q3.shudder@gmail.com']],
            'autoload'          => $autoload,
            'autoload-dev'      => $autoloadDev,
            'minimum-stability' => 'stable',
            'require'           => $composer['require'] ?? null,
            'require-dev'       => $composer['require-dev'] ?? null
        ]);

        return json_encode($newComposer + $composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    }

    private function normalizedAutoload(array $autoload, string $namespace, string $path): array
    {
        $sortedAutoload = [];
        $sortedAutoload['psr-4'] = [$namespace => $path] + $this->autoloadWithoutPath($autoload, $path);
        return $sortedAutoload + $autoload;
    }

    private function autoloadWithoutPath(array $autoload, string $path): array
    {
        $psrAutoload = $autoload['psr-4'] ?? [];
        while ($namespace = array_search($path, $psrAutoload, true)) {
            unset($psrAutoload[$namespace]);
        }

        return $psrAutoload;
    }
}
