<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Subroutine;

use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\Application\FileSystem\File;
use Shudd3r\PackageFiles\Properties;


class GenerateComposer implements Subroutine
{
    private File $file;

    public function __construct(File $composer)
    {
        $this->file = $composer;
    }

    public function process(Properties $data): void
    {
        $composer = json_decode($this->file->contents(), true);

        $namespace = $data->sourceNamespace() . '\\';
        $composer['autoload']['psr-4'][$namespace] = 'src/';
        $composer['autoload-dev']['psr-4'][$namespace . 'Tests\\'] = 'tests/';

        $newComposer = array_filter([
            'name'              => $data->packageName(),
            'description'       => $data->packageDescription(),
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
        $this->file->write($composerJson);
    }
}
