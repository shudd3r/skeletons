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
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Application\Template;


class Initialize extends Command\Factory
{
    public function command(array $options): CommandInterface
    {
        $tokenReader     = $this->env->replacements()->init($options);
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

    protected function processor(): Processor
    {
        $composerFile     = $this->env->package()->file('composer.json');
        $template         = new Template\ComposerJsonTemplate($composerFile);
        $generateComposer = new Processor\GenerateFile($template, $composerFile);

        $generatorFactory = new Processor\FileProcessors\NewFileGenerators($this->env->package());
        $generatePackage  = new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);

        return new Processor\ProcessorSequence($generateComposer, $generatePackage);
    }
}
