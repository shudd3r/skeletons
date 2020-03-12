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

use Shudd3r\PackageFiles\EnvSource\File;
use Shudd3r\PackageFiles\Command\GenerateComposer;


class Build
{
    private $terminal;
    private $resourceDir;

    /**
     * @param Terminal $terminal
     * @param string   $resourceDir
     */
    public function __construct(Terminal $terminal, string $resourceDir)
    {
        $this->terminal    = $terminal;
        $this->resourceDir = $resourceDir;
    }

    /**
     * Builds package environment files.
     *
     * @param array $args
     */
    public function run(array $args = []): void
    {
        $projectRoot = $args[1] ?? getcwd() . DIRECTORY_SEPARATOR . 'build';
        if (!$this->isValidWorkspace($projectRoot)) {
            $this->terminal->display('Project root directory must contain composer.json file');
            return;
        }

        $composerFile = new File($projectRoot . DIRECTORY_SEPARATOR . 'composer.json');

        $composer = json_decode($composerFile->contents(), true);

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

        $command = new GenerateComposer($composerFile);
        $command->execute($data);
    }

    private function isValidWorkspace(string $projectRoot): bool
    {
        if (!is_dir($projectRoot)) { return false; }
        $projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return file_exists($projectRoot . 'composer.json');
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->display($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
