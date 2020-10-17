<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Command\Factory;

use Shudd3r\PackageFiles\Command\CommandSequence;
use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Command\TokenProcessor;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    public function command(): Command
    {
        $reader = new Reader\TokensReader($this->env->output(), ...$this->tokenReaders());
        return new CommandSequence(new TokenProcessor($reader, $this->processor()));
    }

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

    protected function processor(): Processor
    {
        $packageFiles = $this->env->packageFiles();

        $composerFile     = $packageFiles->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Processor\GenerateFile($template, $composerFile);

        $generatorFactory = new Processor\Factory\FileGeneratorFactory($packageFiles);
        $generatePackage  = new Processor\SkeletonFilesProcessor($this->env->skeletonFiles(), $generatorFactory);

        return new Processor\ProcessorSequence($generateComposer, $generatePackage);
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
