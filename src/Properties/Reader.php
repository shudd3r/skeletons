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
use Shudd3r\PackageFiles\Application\Output;


class Reader
{
    private Source $source;
    private Output $output;

    public function __construct(Source $source, Output $output)
    {
        $this->source = $source;
        $this->output = $output;
    }

    public function properties(): ?Properties
    {
        $repository = $this->source->repositoryName();
        if (!$this->isValidRepositoryName($repository)) {
            $this->output->send("Invalid github repository name `{$repository}`", 1);
        }

        $package = $this->source->packageName();
        if (!$this->isValidPackagistPackage($package)) {
            $this->output->send("Invalid packagist package name `{$package}`", 1);
        }

        $description = $this->source->packageDescription();
        if (!$description) {
            $this->output->send('Package description cannot be empty', 1);
        }

        $namespace = $this->source->sourceNamespace();
        if (!$this->isValidNamespace($namespace)) {
            $this->output->send("Invalid namespace `{$namespace}`", 1);
        }

        return $this->output->exitCode() ? null : new Properties($repository, $package, $description, $namespace);
    }

    private function isValidRepositoryName(string $repoName): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[a-z0-9]|-(?=[a-z0-9])){0,38}/[a-z0-9_.-]{1,100}$#iD', $repoName);
    }

    private function isValidPackagistPackage(string $package): bool
    {
        return (bool) preg_match('#^[a-z0-9](?:[_.-]?[a-z0-9]+)*/[a-z0-9](?:[_.-]?[a-z0-9]+)*$#iD', $package);
    }

    private function isValidNamespace(string $namespace): bool
    {
        foreach (explode('\\', $namespace) as $label) {
            if (!preg_match('#^[a-z_\x7f-\xff][a-z0-9_\x7f-\xff]*$#Di', $label)) {
                return false;
            }
        }
        return true;
    }
}
