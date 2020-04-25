<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Properties;

use Shudd3r\PackageFiles\Properties;
use Shudd3r\PackageFiles\Terminal;


class InputProperties extends Properties
{
    private Terminal   $terminal;
    private Properties $properties;
    private string     $repoUrl;

    public function __construct(Terminal $terminal, Properties $properties)
    {
        $this->terminal   = $terminal;
        $this->properties = $properties;
    }

    public function repositoryUrl(): string
    {
        return $this->repoUrl ??= $this->input('Github repository URL', $this->properties->repositoryUrl());
    }

    public function packageName(): string
    {
        return $this->input('Packagist package name', $this->properties->packageName());
    }

    public function packageDescription(): string
    {
        return $this->input('Package description', $this->properties->packageDescription());
    }

    public function sourceNamespace(): string
    {
        return $this->input('Source files namespace', $this->properties->sourceNamespace());
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        $this->terminal->render($prompt . $defaultInfo . ': ');

        return $this->terminal->input() ?: $default;
    }
}
