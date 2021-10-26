<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Commands;

use Shudd3r\PackageFiles\Commands;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Setup\AppSetup;
use Shudd3r\PackageFiles\Replacements;
use Shudd3r\PackageFiles\Processors;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class Initialize implements Commands
{
    use DefineOutputMethods;

    private RuntimeEnv $env;
    private AppSetup   $setup;

    public function __construct(RuntimeEnv $env, AppSetup $setup)
    {
        $this->env   = $env;
        $this->setup = $setup;
    }

    public function command(array $options): Command
    {
        $replacements = $this->setup->replacements();
        $templates    = $this->setup->templates($this->env);

        $initialReader  = new Replacements\Reader\InitialReader($replacements, $this->env, $options);
        $generatedFiles = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupFiles    = new Directory\ReflectedDirectory($this->env->backup(), $generatedFiles);
        $processors     = new Processors\FileGenerators();
        $fileGenerator  = new Processors\Processor\FilesProcessor($generatedFiles, $templates, $processors);

        $noMetaDataFile    = new Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($backupFiles);
        $validReplacements = new Precondition\ValidReplacements($initialReader);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status', $noMetaDataFile),
            $this->checkInfo('Checking backup overwrite', $noBackupOverwrite),
            $this->checkInfo('Gathering replacement values', $validReplacements, false)
        );

        $backupFiles   = new Command\BackupFiles($generatedFiles, $this->env->backup());
        $processTokens = new Command\TokenProcessor($initialReader, $fileGenerator, $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($initialReader, $this->env->metaData());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Moving skeleton files from package to backup directory', $backupFiles),
            $this->commandInfo('Generating skeleton files', $processTokens),
            $this->commandInfo('Generating meta data file', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
