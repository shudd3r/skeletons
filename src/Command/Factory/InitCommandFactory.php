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

use Shudd3r\PackageFiles\Command\BackupFiles;
use Shudd3r\PackageFiles\Command\CommandSequence;
use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\FileSystem\Directory\ReflectedDirectory;
use Shudd3r\PackageFiles\Command\TokenProcessor;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    public function command(): Command
    {
        $packageFiles = new ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupFiles  = new BackupFiles($packageFiles, $this->env->backup());

        $reader        = new Reader\TokensReader($this->env->output(), ...$this->tokenReaders());
        $processTokens = new TokenProcessor($reader, $this->processor());

        return new CommandSequence($backupFiles, $processTokens);
    }

    protected function tokenReaders(): array
    {
        $files    = $this->env->package();
        $composer = new Reader\Data\ComposerJsonData($files->file('composer.json'));

        $package = $this->option('package', new Reader\Source\DefaultPackage($composer, $files));
        $package = $this->errorHandler('Package name', $this->interactive('Packagist package name', $package));
        $package = $this->cached($package);

        $repo = $this->option('repo', new Reader\Source\DefaultRepository($files->file('.git/config'), $package));
        $repo = $this->errorHandler('Repository name', $this->interactive('Github repository name', $repo));

        $desc = $this->option('desc', new Reader\Source\PackageDescription($composer, $package));
        $desc = $this->errorHandler('Package description', $this->interactive('Package description', $desc));

        $namespace = $this->option('ns', new Reader\Source\DefaultNamespace($composer, $package));
        $namespace = $this->errorHandler('Namespace', $this->interactive('Source files namespace', $namespace));

        return [$package, $repo, $desc, $namespace];
    }

    protected function processor(): Processor
    {
        $composerFile     = $this->env->package()->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Processor\GenerateFile($template, $composerFile);

        $generatorFactory = new Processor\Factory\FileGeneratorFactory($this->env->package());
        $generatePackage  = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);

        return new Processor\ProcessorSequence($generateComposer, $generatePackage);
    }

    private function option(string $name, Reader\Source $source): ?Reader\Source
    {
        return isset($this->options[$name])
            ? new Reader\Source\PredefinedString($this->options[$name], $source)
            : $source;
    }

    private function interactive(string $prompt, Reader\Source $source): Reader\Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Reader\Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }

    private function errorHandler(string $tokenName, Reader\Source $source): Reader\Source
    {
        return new Reader\Source\ErrorMessageOutput($source, $this->env->output(), $tokenName);
    }

    private function cached(Reader\Source $source): Reader\Source
    {
        return new Reader\Source\CachedValue($source);
    }
}
