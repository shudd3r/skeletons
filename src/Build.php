<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Command\GenerateComposer;
use Shudd3r\PackageFiles\Files\File;


class Build
{
    private $terminal;
    private $skeletonFiles;
    private $packageFiles;

    /**
     * @param Terminal $terminal
     * @param Files    $skeletonFiles
     * @param Files    $packageFiles
     */
    public function __construct(Terminal $terminal, Files $skeletonFiles, Files $packageFiles)
    {
        $this->terminal      = $terminal;
        $this->skeletonFiles = $skeletonFiles;
        $this->packageFiles  = $packageFiles;
    }

    /**
     * Builds package environment files.
     *
     * $options array is option name keys ('package', 'repo' & 'desc') with
     * corresponding values. Not provided option values might be omitted or
     * assigned to null.
     *
     * @example Array with all values defined for this package: [
     *     'package' => 'polymorphine/dev',
     *     'repo'    => 'polymorphine/dev',
     *     'desc'    => 'Development tools & coding standard scripts for Polymorphine libraries'
     * ]
     *
     * @param array $options
     */
    public function run(array $options = []): void
    {
        $composer = json_decode($this->packageFiles->contents('composer.json'), true);

        $package     = $options['package'] ?? $composer['name'] ?? '';
        $repo        = $options['repo'] ?? '';
        $description = $options['desc'] ?? $composer['description'] ?? '';

        if (empty($options['package'])) {
            $package = $this->input('Packagist package name', $package ?: $this->packageNameFromDirectory());
        }

        if (empty($options['desc'])) {
            $description = $this->input('Package description', $description ?: 'Polymorphine library package');
        }

        if (!$repo) {
            $repo = $this->input('Github repository URL', 'https://github.com/' . $package . '.git');
        }

        $data = new Properties($repo, $package, $description);

        $composerFile = new File('composer.json', $this->packageFiles);
        $command      = new GenerateComposer($composerFile);

        $command->execute($data);
    }

    private function packageNameFromDirectory(): string
    {
        $directory = $this->packageFiles->directory();
        return basename(dirname($directory)) . '/' . basename($directory);
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->display($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
