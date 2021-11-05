<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Skeletons package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\Skeletons\Commands;

use Shudd3r\Skeletons\Replacements\Reader\InitialReader;
use Shudd3r\Skeletons\Environment\Files;


class Initialize extends Factory
{
    public function command(array $options): Command
    {
        $isInteractive = isset($options['i']) || isset($options['interactive']);
        $metaFilename  = $this->env->metaDataFile()->name();

        $initialTokens  = new InitialReader($this->replacements, $this->env, $options);
        $expectedBackup = new Files\ReflectedFiles($this->env->backup(), $this->generatedFiles);

        $noMetaDataFile    = new Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($expectedBackup);
        $validReplacements = new Precondition\ValidReplacements($initialTokens);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $metaFilename . '` should not exist)', $noMetaDataFile),
            $this->checkInfo('Checking backup overwrite', $noBackupOverwrite),
            $this->checkInfo('Gathering replacement values', $validReplacements, !$isInteractive)
        );

        $backupFiles   = new Command\BackupFiles($this->generatedFiles, $this->env->backup());
        $generateFiles = new Command\ProcessTokens($initialTokens, $this->filesGenerator(), $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($initialTokens, $this->env->metaData());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Moving existing skeleton files to backup directory', $backupFiles),
            $this->commandInfo('Generating skeleton files:', $generateFiles),
            $this->commandInfo('Generating meta data file (`' . $metaFilename . '`)', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
