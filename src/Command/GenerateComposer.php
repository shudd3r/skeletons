<?php declare(strict_types=1);

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
use Shudd3r\PackageFiles\Files;
use Shudd3r\PackageFiles\Properties;


class GenerateComposer implements Command
{
    private Files $files;

    public function __construct(Files $files)
    {
        $this->files = $files;
    }

    public function execute(Properties $data): void
    {
        $composer  = json_decode($this->files->contents('composer.json'), true);
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
        $this->files->write('composer.json', $composerJson);
    }
}
