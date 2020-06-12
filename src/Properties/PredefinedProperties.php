<?php declare(strict_types=1);

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


class PredefinedProperties extends Properties
{
    private Properties $default;
    private array      $defined;

    public function __construct(array $definedOptions, Properties $default)
    {
        $this->default = $default;
        $this->defined = $definedOptions;
    }

    public function repositoryUrl(): string
    {
        return $this->defined['repo'] ?? $this->default->repositoryUrl();
    }

    public function packageName(): string
    {
        return $this->defined['package'] ?? $this->default->packageName();
    }

    public function packageDescription(): string
    {
        return $this->defined['desc'] ?? $this->default->packageDescription();
    }

    public function sourceNamespace(): string
    {
        return $this->defined['ns'] ?? $this->default->sourceNamespace();
    }
}
