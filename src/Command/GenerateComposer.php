<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command;

use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\EnvSource;


class GenerateComposer implements Command
{
    private EnvSource $composer;

    public function __construct(EnvSource $composer)
    {
        $this->composer = $composer;
    }

    public function execute(Properties $data): void
    {
        $composer  = json_decode($this->composer->contents(), true);
        $namespace = ucfirst($data->packageVendor) . '\\' . ucfirst($data->packageName) . '\\';
        $composer['autoload']['psr-4'][$namespace] = 'src/';
        $composer['autoload-dev']['psr-4'][$namespace . 'Tests\\'] = 'tests/';

        $newComposer = array_filter([
            'name'              => $data->packageVendor . '/' . $data->packageName,
            'description'       => $data->packageDesc,
            'type'              => 'library',
            'license'           => 'MIT',
            'authors'           => $composer['authors'] ?? [['name' => 'Shudd3r', 'email' => 'q3.shudder@gmail.com']],
            'autoload'          => $composer['autoload'],
            'autoload-dev'      => $composer['autoload-dev'],
            'minimum-stability' => 'stable',
            'require'           => $composer['require'] ?? null,
            'require-dev'       => $composer['require-dev'] ?? null
        ]);

        $composerJson = json_encode($newComposer + $composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        $this->composer->write($composerJson);
    }
}
