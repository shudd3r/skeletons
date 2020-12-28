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

use Shudd3r\PackageFiles\Command;
use Shudd3r\PackageFiles\Environment\Command as CommandInterface;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Token\Source;
use Shudd3r\PackageFiles\Token\Reader;
use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Template;


class InitCommandFactory extends Command\Factory
{
    public function command(): CommandInterface
    {
        $tokenReader     = new Reader\CompositeTokenReader(...$this->tokenReaders());
        $generatedFiles  = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupDirectory = $this->env->backup();

        $backupFiles   = new Command\BackupFiles($generatedFiles, $backupDirectory);
        $processTokens = new Command\TokenProcessor($tokenReader, $this->processor());
        $writeMetaData = new Command\WriteMetaData($tokenReader, $this->env->metaDataFile());

        $noMetaDataFile    = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Command\Precondition\CheckFilesOverwrite($generatedFiles, $backupDirectory);

        return new Command\ProtectedCommand(
            new Command\CommandSequence($backupFiles, $processTokens, $writeMetaData),
            new Command\Precondition\Preconditions($noMetaDataFile, $noBackupOverwrite)
        );
    }

    protected function tokenReaders(): array
    {
        $files    = $this->env->package();
        $composer = new Reader\Data\ComposerJsonData($files->file('composer.json'));

        $source  = $this->interactive('Packagist package name', $this->option('package'));
        $package = new Reader\PackageName($composer, $files, $source);

        $source = $this->interactive('Github repository name', $this->option('repo'));
        $repo   = new Reader\RepositoryName($files->file('.git/config'), $package, $source);

        $source = $this->interactive('Github repository name', $this->option('desc'));
        $desc   = new Reader\PackageDescription($composer, $package, $source);

        $source    = $this->interactive('Source files namespace', $this->option('ns'));
        $namespace = new Reader\SrcNamespace($composer, $package, $source);

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

    private function option(string $name): Source
    {
        return isset($this->options[$name])
            ? new Source\PredefinedValue($this->options[$name])
            : new Source\ParsedFiles();
    }

    private function interactive(string $prompt, Source $source): Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
