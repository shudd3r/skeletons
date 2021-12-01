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
use Shudd3r\Skeletons\Rework\Replacements\Reader;
use Shudd3r\Skeletons\Rework\Replacements\Tokens;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Processors;


class Synchronize extends Factory
{
    public function command(InputArgs $args): Command
    {
        $files     = $this->templates->generatedFiles($args);
        $backup    = new Files\ReflectedFiles($this->env->backup(), $files);
        $dummies   = $this->templates->dummyFiles();
        $tokens    = new Tokens($this->replacements, new Reader\DataReader($this->env, $args));
        $processor = $this->filesProcessor($files, $this->mismatchedFileGenerators());

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $validReplacements = new Precondition\ValidReplacements($tokens, $this->output);
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($backup);
        $preconditions     = new Precondition\Preconditions(
            $this->checkInfo($metaDataExists, 'Looking for meta data file (`' . $this->metaFile . '`)', 8),
            $this->checkInfo($validReplacements, 'Validating meta data replacements', 16, ['OK']),
            $this->checkInfo($noBackupOverwrite, 'Backup should not overwrite files', 32)
        );

        $generateFiles = new Command\ProcessTokens($tokens, $processor, $this->output);
        $command       = new Command\CommandSequence(
            $this->commandInfo($generateFiles, 'Generating missing or mismatched skeleton files (with backup):'),
            new Command\HandleDummyFiles($this->env->package(), $dummies, $this->output)
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
