<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles;

use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Environment\Command as CommandInterface;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;


class Initialize extends Command\Factory
{
    public function command(): CommandInterface
    {
        $tokenReader     = new Reader\CompositeTokenReader(...$this->tokenReaders());
        $generatedFiles  = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupDirectory = $this->env->backup();

        $backupFiles   = new Command\BackupFiles($generatedFiles, $backupDirectory);
        $processTokens = new Command\TokenProcessor($tokenReader, $this->processor(), $this->env->output());
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
        $composer = new Source\Data\ComposerJsonData($files->file('composer.json'));

        $source  = new Source\DefaultPackageName($composer, $files);
        $source  = $this->interactive('Packagist package name', $this->option('package', $source));
        $package = new Reader\PackageName($source);

        $source = new Source\DefaultRepositoryName($files->file('.git/config'), $package);
        $source = $this->interactive('Github repository name', $this->option('repo', $source));
        $repo   = new Reader\RepositoryName($source);

        $source = new Source\DefaultPackageDescription($composer, $package);
        $source = $this->interactive('Github repository name', $this->option('desc', $source));
        $desc   = new Reader\PackageDescription($source);

        $source    = new Source\DefaultSrcNamespace($composer, $package);
        $source    = $this->interactive('Source files namespace', $this->option('ns', $source));
        $namespace = new Reader\SrcNamespace($source);

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

    private function option(string $name, Source $default): Source
    {
        return isset($this->options[$name])
            ? new Source\PredefinedValue($this->options[$name])
            : $default;
    }

    private function interactive(string $prompt, Source $source): Source
    {
        return isset($this->options['i']) || isset($this->options['interactive'])
            ? new Source\InteractiveInput($prompt, $this->env->input(), $source)
            : $source;
    }
}
