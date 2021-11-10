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

use Shudd3r\Skeletons\InputArgs;
use Shudd3r\Skeletons\Replacements\Reader;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Processors;


class Initialize extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files     = $this->templates->generatedFiles($args);
        $backup    = new Files\ReflectedFiles($this->env->backup(), $files);
        $tokens    = new Reader\InitialReader($this->replacements, $this->env, $args);
        $processor = $this->filesProcessor($files, $this->mismatchedFileGenerators());

        $noMetaDataFile    = new Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($backup);
        $validReplacements = new Precondition\ValidReplacements($tokens);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $this->metaFile . '` should not exist)', $noMetaDataFile),
            $this->checkInfo('Checking backup overwrite', $noBackupOverwrite),
            $this->checkInfo('Gathering replacement values', $validReplacements, !$args->interactive())
        );

        $generateFiles = new Command\ProcessTokens($tokens, $processor, $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($tokens, $this->env->metaData());
        $command       = new Command\CommandSequence(
            $this->commandInfo('Generating skeleton files:', $generateFiles),
            $this->commandInfo('Generating meta data file (`' . $this->metaFile . '`)', $saveMetaData)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }

    private function mismatchedFileGenerators(): Processors
    {
        $fileValidators = $this->fileValidators(null, $this->env->backup());
        $fileGenerators = $this->fileGenerators();
        return new Processors\FallbackProcessors($fileValidators, $fileGenerators);
    }
}
