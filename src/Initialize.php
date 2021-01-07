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
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Source;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;


class Initialize extends Command\Factory
{
    private Source\Data\ComposerJsonData $composer;

    public function command(): CommandInterface
    {
        $tokenReader     = new Reader\CompositeTokenReader($this->tokenReaders());
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

    protected function source(string $readerName, array $readers): Source
    {
        $this->composer ??= new Source\Data\ComposerJsonData($this->env->package()->file('composer.json'));

        switch ($readerName) {
            default:
            case Command\Factory::PACKAGE_NAME:
                $source = new Source\DefaultPackageName($this->composer, $this->env->package());
                return $this->interactive('Packagist package name', $this->option('package', $source));
            case Command\Factory::PACKAGE_DESC:
                $source = new Source\DefaultPackageDescription($this->composer, $readers[self::PACKAGE_NAME]);
                return $this->interactive('Package description', $this->option('desc', $source));
            case Command\Factory::SRC_NAMESPACE:
                $source = new Source\DefaultSrcNamespace($this->composer, $readers[self::PACKAGE_NAME]);
                return $this->interactive('Source files namespace', $this->option('ns', $source));
            case Command\Factory::REPO_NAME:
                $config = $this->env->package()->file('.git/config');
                $source = new Source\DefaultRepositoryName($config, $readers[self::PACKAGE_NAME]);
                return $this->interactive('Github repository name', $this->option('repo', $source));
        }
    }

    protected function processor(): Processor
    {
        $composerFile     = $this->env->package()->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Processor\GenerateFile($template, $composerFile);

        $generatorFactory = new Processor\FileProcessors\NewFileGenerators($this->env->package());
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
