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

use Shudd3r\PackageFiles\Resource\File;


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

        $vendorName  = $this->input('Vendor name', $vendorName);
        $packageName = $this->input('Package name', $packageName);
        $description = $this->input('Package Description', $description);
        $vendorRepo  = $this->input('Package Github account', $vendorName);
        $packageRepo = $this->input('Package Github repository', $packageName);

        $vendorNamespace  = ucfirst($vendorName);
        $packageNamespace = ucfirst($packageName);

        $composer['autoload']['psr-4'][$vendorNamespace . '\\' . $packageNamespace . '\\']            = 'src/';
        $composer['autoload-dev']['psr-4'][$vendorNamespace . '\\' . $packageNamespace . '\\Tests\\'] = 'tests/';

        $newComposer = array_filter([
            'name'              => $vendorName . '/' . $packageName,
            'description'       => $description,
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
        $composerFile->write($composerJson);

        $this->terminal->display(
            PHP_EOL .
            "composer package  = $vendorName/$packageName\n" .
            "package namespace = $vendorNamespace\\$packageNamespace\n" .
            "github repository = $vendorRepo/$packageRepo\n"
        );
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
