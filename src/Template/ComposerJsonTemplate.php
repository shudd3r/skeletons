<?php

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

        if (isset($composer['autoload']['psr-4'])) {
            $this->removeAutoloadForPath($composer['autoload']['psr-4'], 'src/');
        }

        if (isset($composer['autoload-dev']['psr-4'])) {
            $this->removeAutoloadForPath($composer['autoload-dev']['psr-4'], 'tests/');
        }

        $namespace = $properties->sourceNamespace() . '\\';
        $autoload['psr-4']    = [$namespace => 'src/'] + $composer['autoload']['psr-4'];
        $autoloadDev['psr-4'] = [$namespace . 'Tests\\' => 'tests/'] + $composer['autoload-dev']['psr-4'];

        $newComposer = array_filter([
            'name'              => $properties->packageName(),
            'description'       => $properties->packageDescription(),
            'type'              => 'library',
            'license'           => 'MIT',
            'authors'           => $composer['authors'] ?? [['name' => 'Shudd3r', 'email' => 'q3.shudder@gmail.com']],
            'autoload'          => $autoload + $composer['autoload'],
            'autoload-dev'      => $autoloadDev + $composer['autoload-dev'],
            'minimum-stability' => 'stable',
            'require'           => $composer['require'] ?? null,
            'require-dev'       => $composer['require-dev'] ?? null
        ]);

        return json_encode($newComposer + $composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }

    private function removeAutoloadForPath(array &$autoload, string $removePath): void
    {
        foreach ($autoload as $namespace => $path) {
            if ($path !== $removePath) { continue; }
            unset($autoload[$namespace]);
        }
    }
}
