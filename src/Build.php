<?php

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
use InvalidArgumentException;


class Build
{
    private $terminal;
    private $skeletonFiles;

    /**
     * @param Terminal $terminal
     * @param Files    $skeletonFiles
     */
    public function __construct(Terminal $terminal, Files $skeletonFiles)
    {
        $this->terminal      = $terminal;
        $this->skeletonFiles = $skeletonFiles;
    }

    /**
     * Builds package environment files.
     *
     * @param array $args
     */
    public function run(array $args = []): void
    {
        $projectRoot = $args[1] ?? getcwd() . DIRECTORY_SEPARATOR . 'build';

        try {
            $files = new ProjectFiles($projectRoot);
        } catch (InvalidArgumentException $e) {
            $this->terminal->display($e->getMessage());
            return;
        }

        if (!$files->exists('composer.json')) {
            $this->terminal->display('Project root directory must contain composer.json file');
            return;
        }

        $composer = json_decode($files->contents('composer.json'), true);

        [$vendorName, $packageName] = isset($composer['name'])
            ? explode('/', $composer['name'])
            : [basename(dirname($projectRoot)), basename($projectRoot)];
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
