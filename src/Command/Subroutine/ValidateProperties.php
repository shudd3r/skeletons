<?php

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Subroutine;

use Shudd3r\PackageFiles\Command\Subroutine;
use Shudd3r\PackageFiles\Application\Output;
use Shudd3r\PackageFiles\Properties;


class ValidateProperties implements Subroutine
{
    private Output     $output;
    private Subroutine $nextSubroutine;

    public function __construct(Output $output, Subroutine $nextSubroutine)
    {
        $this->output         = $output;
        $this->nextSubroutine = $nextSubroutine;
    }

    public function process(Properties $options): void
    {
        $githubUri = $options->repositoryUrl();
        if (!$this->isValidGithubUri($githubUri)) {
            $this->output->render("Invalid github uri `{$githubUri}`", 1);
        }

        $packageName = $options->packageName();
        if (!$this->isValidPackagistPackage($packageName)) {
            $this->output->render("Invalid packagist package name `{$packageName}`", 1);
        }

        $namespace = $options->sourceNamespace();
        if (!$this->isValidNamespace($namespace)) {
            $this->output->render("Invalid namespace `{$namespace}`", 1);
        }

        if (empty($options->packageDescription())) {
            $this->output->render('Package description cannot be empty', 1);
        }

        if ($this->output->exitCode() !== 0) { return; }
        $this->nextSubroutine->process($options);
    }

    private function isValidGithubUri(string $uri): bool
    {
        $validSuffix = substr($uri, -4) === '.git';
        $validPrefix = substr($uri, 0, 19) === 'https://github.com/' || substr($uri, 0, 15) === 'git@github.com:';

        if (!$validPrefix || !$validSuffix) { return false; }

        $repoName = $uri[0] === 'h' ? substr($uri, 19, -4) : substr($uri, 15, -4);
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
