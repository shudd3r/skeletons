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


class PackageBuild
{
    private $input;
    private $resourceDir;

    /**
     * @param callable $input       fn(prompt, defaultValue) => string
     * @param string   $resourceDir
     */
    public function __construct(callable $input, string $resourceDir)
    {
        $this->input       = $input;
        $this->resourceDir = $resourceDir;
    }

    /**
     * Builds package environment files.
     *
     * @param array $args
     */
    public function run(array $args): void
    {
        $projectRoot = $args[0];
        if (!$this->isValidWorkspace($projectRoot)) {
            echo 'Package can be initialized in directory containing vendor directory';
            exit;
        }

        $composerFile = $projectRoot . DIRECTORY_SEPARATOR . 'composer.json';
        $composer     = json_decode(file_get_contents($composerFile), true);

        [$vendorName, $packageName] = isset($composer['name'])
            ? explode('/', $composer['name'])
            : [basename(dirname($projectRoot)), basename($projectRoot)];
        $description = $composer['description'] ?? 'Polymorphine library package';

        $input       = $this->input;
        $vendorName  = $input('Vendor name', $vendorName);
        $packageName = $input('Package name', $packageName);
        $description = $input('Package Description', $description);

        $vendorNamespace  = ucfirst($vendorName);
        $packageNamespace = ucfirst($packageName);

        $vendorRepository  = $input('Vendor Github', $vendorName);
        $packageRepository = $input('Package Github repository', $packageName);

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
        file_put_contents($composerFile, $composerJson);

        echo PHP_EOL;
        echo "composer package  = $vendorName/$packageName\n";
        echo "package namespace = $vendorNamespace\\$packageNamespace\n";
        echo "github repository = $vendorRepository/$packageRepository\n";
    }

    private function isValidWorkspace(string $projectRoot)
    {
        if (!is_dir($projectRoot)) { return false; }
        $projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!file_exists($projectRoot . 'composer.json')) { return false; }
        return true;
    }
}
