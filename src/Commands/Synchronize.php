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

use Shudd3r\Skeletons\Replacements;
use Shudd3r\Skeletons\Environment\Files;
use Shudd3r\Skeletons\Processors;


class Synchronize extends Factory
{
    public function command(array $options): Command
    {
        $files = $this->templates->generatedFiles(isset($options['remote']) ? ['local', 'init'] : ['init']);

        $metaFilename     = $this->env->metaDataFile()->name();
        $validationTokens = new Replacements\Reader\ValidationReader($this->replacements, $this->env, $options);
        $expectedBackup   = new Files\ReflectedFiles($this->env->backup(), $files);

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile());
        $noBackupOverwrite = new Precondition\CheckFilesOverwrite($expectedBackup);
        $validReplacements = new Precondition\ValidReplacements($validationTokens);
        $precondition      = new Precondition\Preconditions(
            $this->checkInfo('Checking meta data status (`' . $metaFilename . '` should exist)', $metaDataExists),
            $this->checkInfo('Checking backup overwrite', $noBackupOverwrite),
            $this->checkInfo('Validating meta data replacements', $validReplacements)
        );

        $fileValidators = new Processors\FileValidators($this->env->output(), null, $this->env->backup());
        $fileGenerators = new Processors\FileGenerators($this->env->output());
        $synchronizer   = new Processors\FallbackProcessors($fileValidators, $fileGenerators);
        $filesProcessor = new Processors\Processor\FilesProcessor($files, $this->templates, $synchronizer);
        $processTokens  = new Command\ProcessTokens($validationTokens, $filesProcessor, $this->env->output());

        $command = $this->commandInfo('Generating missing/divergent skeleton files (with backup):', $processTokens);
        return new Command\ProtectedCommand($command, $precondition, $this->env->output());
    }
}
