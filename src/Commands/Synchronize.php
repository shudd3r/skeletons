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


class Synchronize extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files     = $this->templates->generatedFiles($args);
        $backup    = new Files\ReflectedFiles($this->env->backup(), $files);
        $dummies   = $this->templates->dummyFiles();
        $tokens    = new Reader\ValidationReader($this->replacements, $this->env, $args);
        $processor = $this->filesProcessor($files, $this->mismatchedFileGenerators());

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validReplacements = new Precondition\ValidReplacements($tokens, $this->output);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($backup);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo('Looking for meta data file (`' . $this->metaFile . '`)', $metaDataExists),
            $this->checkInfo('Validating meta data replacements', $validReplacements, ['OK']),
            $this->checkInfo('Backup should not overwrite files', $noBackupOverwrite)
        );

        $generateFiles = new Command\ProcessTokens($tokens, $processor, $this->output);
        $command       = new Command\CommandSequence(
            $this->commandInfo('Generating skeleton files (with backup):', $generateFiles),
            new Command\HandleRedundantFiles($this->env->package(), $dummies, $this->output)
        );

        return new Command\ProtectedCommand($command, $preconditions, $this->output);
    }

    private function mismatchedFileGenerators(): Processors
    {
        $fileValidators = $this->fileValidators(null, $this->env->backup());
        $fileGenerators = $this->fileGenerators();
        return new Processors\FallbackProcessors($fileValidators, $fileGenerators);
    }
}
