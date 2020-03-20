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
     * @param array $args
     */
    public function run(array $args = []): void
    {
        $composer = json_decode($this->packageFiles->contents('composer.json'), true);

        [$vendorName, $packageName] = isset($composer['name'])
            ? explode('/', $composer['name'])
            : [basename(dirname($this->packageFiles->directory())), basename($this->packageFiles->directory())];
        $description = $composer['description'] ?? 'Polymorphine library package';

        $data = new Properties();
        $data->packageVendor = $this->input('Vendor name', $vendorName);
        $data->packageName   = $this->input('Package name', $packageName);
        $data->packageDesc   = $this->input('Package Description', $description);
        $data->repoUser      = $this->input('Package Github account', $vendorName);
        $data->repoName      = $this->input('Package Github repository', $packageName);

        $command = new GenerateComposer($this->packageFiles);
        $command->execute($data);
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->display($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
