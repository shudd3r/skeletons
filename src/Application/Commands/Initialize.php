<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Application\Commands;

use Shudd3r\PackageFiles\Application\Commands;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Replacements;
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class Initialize implements Commands
{
    private RuntimeEnv $env;
    private array      $options;

    public function __construct(RuntimeEnv $env, array $options)
    {
        $this->env     = $env;
        $this->options = $options;
    }

    public function command(Replacements $replacements, Templates $templates): Command
    {
        $initialReader  = new Replacements\Reader\InitialReader($replacements, $this->env, $this->options);
        $generatedFiles = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupFiles    = new Directory\ReflectedDirectory($this->env->backup(), $generatedFiles);
        $fileGenerator  = new Processor\FilesProcessor\FilesGenerator($generatedFiles, $templates);

        $noMetaDataFile    = new Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($backupFiles);
        $validReplacements = new Precondition\ValidReplacements($initialReader);
        $preconditions     = new Precondition\Preconditions($noMetaDataFile, $noBackupOverwrite, $validReplacements);

        $backupFiles   = new Command\BackupFiles($generatedFiles, $this->env->backup());
        $processTokens = new Command\TokenProcessor($initialReader, $fileGenerator, $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($initialReader, $this->env->metaData());
        $command       = new Command\CommandSequence($backupFiles, $processTokens, $saveMetaData);

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
