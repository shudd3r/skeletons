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

use Shudd3r\PackageFiles\Files\ProjectFiles;
use Shudd3r\PackageFiles\Command\GenerateComposer;


class Build
{
    private $terminal;
    private $skeletonFiles;
    private $rootDirectory;

    /**
     * @param Terminal $terminal
     * @param Files    $skeletonFiles
     * @param string   $rootDirectory
     */
    public function __construct(Terminal $terminal, Files $skeletonFiles, string $rootDirectory)
    {
        $this->terminal      = $terminal;
        $this->skeletonFiles = $skeletonFiles;
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * Builds package environment files.
     *
     * @param array $args
     */
    public function run(array $args = []): void
    {
        $files    = new ProjectFiles($this->rootDirectory);
        $composer = json_decode($files->contents('composer.json'), true);

        [$vendorName, $packageName] = isset($composer['name'])
            ? explode('/', $composer['name'])
            : [basename(dirname($this->rootDirectory)), basename($this->rootDirectory)];
        $description = $composer['description'] ?? 'Polymorphine library package';

        $data = new Properties();
        $data->packageVendor = $this->input('Vendor name', $vendorName);
        $data->packageName   = $this->input('Package name', $packageName);
        $data->packageDesc   = $this->input('Package Description', $description);
        $data->repoUser      = $this->input('Package Github account', $vendorName);
        $data->repoName      = $this->input('Package Github repository', $packageName);

        $command = new GenerateComposer($files);
        $command->execute($data);
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->display($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
