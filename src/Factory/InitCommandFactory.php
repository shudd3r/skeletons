<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Factory;

use Shudd3r\PackageFiles\Factory;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Subroutine;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    protected function tokenReaders(): array
    {
        $files    = $this->env->packageFiles();
        $composer = new Reader\Data\ComposerJsonData($files->file('composer.json'));

        $source  = $this->option('package') ?? new Reader\Source\DefaultPackage($composer, $files);
        $package = new Reader\PackageReader($this->interactive('Packagist package name', $source));

        $source = $this->option('repo') ?? new Reader\Source\DefaultRepository($files->file('.git/config'), $package);
        $repo   = new Reader\RepositoryReader($this->interactive('Github repository name', $source));

        $callback = fn() => $composer->value('description') ?? $package->value() . ' package';
        $source   = $this->option('desc') ?? new Reader\Source\CallbackSource($callback);
        $desc     = new Reader\DescriptionReader($this->interactive('Package description', $source));

        $source    = $this->option('ns') ?? new Reader\Source\DefaultNamespace($composer, $package);
        $namespace = new Reader\NamespaceReader($this->interactive('Source files namespace', $source));

        return [$package, $repo, $desc, $namespace];
    }

    protected function subroutine(): Subroutine
    {
        $packageFiles = $this->env->packageFiles();

        $composerFile     = $packageFiles->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Subroutine\GenerateFile($template, $composerFile);

        $templateFile     = $this->env->skeletonFiles()->file('.github/package.properties');
        $metaDataFile     = $packageFiles->file('.github/package.properties');
        $template         = new Template\FileTemplate($templateFile);
        $generateMetaFile = new Subroutine\GenerateFile($template, $metaDataFile);

        return new Subroutine\SubroutineSequence($generateComposer, $generateMetaFile);
    }

    private function option(string $name): ?Reader\Source
    {
        return isset($this->options[$name]) ? new Reader\Source\PredefinedString($this->options[$name]) : null;
    }

    private function interactive(string $prompt, Reader\Source $source): Reader\Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Reader\Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
