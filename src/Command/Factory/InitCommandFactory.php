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

use Shudd3r\PackageFiles\Command\Factory;
use Shudd3r\PackageFiles\Command\CommandSequence;
use Shudd3r\PackageFiles\Command\TokenProcessor;
use Shudd3r\PackageFiles\Command\BackupFiles;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\FileSystem\Directory\ReflectedDirectory;
use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\TokenV2 as v2;
use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Factory
{
    public function command(): Command
    {
        $packageFiles = new ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupFiles  = new BackupFiles($packageFiles, $this->env->backup());

        $reader        = new v2\Reader\CompositeTokenReader(...$this->tokenReaders());
        $processTokens = new TokenProcessor($reader, $this->processor());

        return new CommandSequence($backupFiles, $processTokens);
    }

    protected function tokenReaders(): array
    {
        $files    = $this->env->package();
        $composer = new Source\Data\ComposerJsonData($files->file('composer.json'));

        $source  = $this->interactive('Packagist package name', $this->option('package'));
        $package = new v2\Reader\PackageName($composer, $files, $source);

        $source = $this->interactive('Github repository name', $this->option('repo'));
        $repo   = new v2\Reader\RepositoryName($files->file('.git/config'), $package, $source);

        $source = $this->interactive('Github repository name', $this->option('desc'));
        $desc   = new v2\Reader\PackageDescription($composer, $package, $source);

        $source    = $this->interactive('Source files namespace', $this->option('ns'));
        $namespace = new v2\Reader\SrcNamespace($composer, $package, $source);

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

    private function option(string $name): v2\Source
    {
        return isset($this->options[$name])
            ? new v2\Source\PredefinedValue($this->options[$name])
            : new v2\Source\ParsedFiles();
    }

    private function interactive(string $prompt, v2\Source $source): v2\Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new v2\Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
