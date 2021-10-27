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

use Shudd3r\PackageFiles\Replacements\Reader\InitialReader;


class Initialize extends CommandFactory
{
    public function command(array $options): Command
    {
        $initialTokens = new InitialReader($this->replacements, $this->env, $options);

        $noMetaDataFile    = new Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($this->backup());
        $validReplacements = new Precondition\ValidReplacements($initialTokens);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status', $noMetaDataFile),
            $this->checkInfo('Checking backup overwrite', $noBackupOverwrite),
            $this->checkInfo('Gathering replacement values', $validReplacements, false)
        );

        $backupFiles   = new Command\BackupFiles($this->files(), $this->env->backup());
        $generateFiles = new Command\ProcessTokens($initialTokens, $this->filesGenerator(), $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($initialTokens, $this->env->metaData());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Moving skeleton files from package to backup directory', $backupFiles),
            $this->commandInfo('Generating skeleton files', $generateFiles),
            $this->commandInfo('Generating meta data file', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
