<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Properties\Source;

use Shudd3r\PackageFiles\Properties\Source;
use Shudd3r\PackageFiles\Application\Input;


class InteractiveInput implements Source
{
    private Input  $input;
    private Source $default;

    public function __construct(Input $input, Source $default)
    {
        $this->input   = $input;
        $this->default = $default;
    }

    public function repositoryName(): string
    {
        return $this->input('Github repository name', $this->default->repositoryName());
    }

    public function packageName(): string
    {
        return $this->input('Packagist package name', $this->default->packageName());
    }

    public function packageDescription(): string
    {
        return $this->input('Package description', $this->default->packageDescription());
    }

    public function sourceNamespace(): string
    {
        return $this->input('Source files namespace', $this->default->sourceNamespace());
    }

    private function input(string $prompt, string $default = ''): string
    {
        $defaultInfo = $default ? ' [default: ' . $default . ']' : '';
        return $this->input->value($prompt . $defaultInfo . ': ') ?: $default;
    }
}
