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


class Build
{
    private $input;
    private $output;
    private $resourceDir;

    /**
     * @param callable $input       fn(prompt, defaultValue) => string
     * @param callable $output      fn(message) => void
     * @param string   $resourceDir
     */
    public function __construct(callable $input, callable $output, string $resourceDir)
    {
        $this->input       = $input;
        $this->output      = $output;
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
            $this->output('Package can be initialized in directory containing vendor directory');
            return;
        }

        $composerFile = $projectRoot . DIRECTORY_SEPARATOR . 'composer.json';
        $composer     = json_decode(file_get_contents($composerFile), true);

        [$vendorName, $packageName] = isset($composer['name'])
            ? explode('/', $composer['name'])
            : [basename(dirname($projectRoot)), basename($projectRoot)];
        $description = $composer['description'] ?? 'Polymorphine library package';

        $vendorName  = $this->input('Vendor name', $vendorName);
        $packageName = $this->input('Package name', $packageName);
        $description = $this->input('Package Description', $description);

        $vendorNamespace  = ucfirst($vendorName);
        $packageNamespace = ucfirst($packageName);

        $vendorRepository  = $this->input('Vendor Github', $vendorName);
        $packageRepository = $this->input('Package Github repository', $packageName);

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

        $this->output(
            PHP_EOL .
            "composer package  = $vendorName/$packageName\n" .
            "package namespace = $vendorNamespace\\$packageNamespace\n" .
            "github repository = $vendorRepository/$packageRepository\n"
        );
    }

    private function isValidWorkspace(string $projectRoot)
    {
        if (!is_dir($projectRoot)) { return false; }
        $projectRoot = rtrim($projectRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!file_exists($projectRoot . 'composer.json')) { return false; }
        return true;
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->output($prompt . $defaultInfo . ': ');

        $line = ($this->input)();

        if ($line === '!') {
            $this->output('...cancelled' . PHP_EOL);
            exit;
        }

        return $line ?: $default;
    }

    private function output(string $message): void
    {
        ($this->output)($message);
    }
}
