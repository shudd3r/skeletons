<?php declare(strict_types=1);

/*
 * This file is part of Shudd3r/Package-Files package.
 *
 * (c) Shudd3r <q3.shudder@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Shudd3r\PackageFiles\Factory;

use Shudd3r\PackageFiles\Factory;
use Shudd3r\PackageFiles\Application\Command;
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Application\Token\Reader;
use Shudd3r\PackageFiles\Application\Token\Replacements;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Processor;


class Initialize implements Factory
{
    private RuntimeEnv   $env;
    private Replacements $replacements;

    public function __construct(RuntimeEnv $env, Replacements $replacements)
    {
        $this->env          = $env;
        $this->replacements = $replacements;
    }

    public function command(array $options): Command
    {
        $tokenReader     = new Reader\InitialReader($this->replacements, $this->env, $options);
        $generatedFiles  = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupDirectory = $this->env->backup();
        $output          = $this->env->output();

        $backupFiles   = new Command\BackupFiles($generatedFiles, $backupDirectory);
        $processTokens = new Command\TokenProcessor($tokenReader, $this->processor(), $output);
        $saveMetaData  = new Command\SaveMetaData($tokenReader, $this->env->metaData());

        $noMetaDataFile    = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Command\Precondition\CheckFilesOverwrite($generatedFiles, $backupDirectory);

        return new Command\ProtectedCommand(
            new Command\CommandSequence($backupFiles, $processTokens, $saveMetaData),
            new Command\Precondition\Preconditions($noMetaDataFile, $noBackupOverwrite),
            $output
        );
    }

    protected function processor(): Processor
    {
        $generatorFactory = new Processor\FileProcessors\NewFileGenerators($this->env->package(), $this->env->templates());
        return new Processor\SkeletonFilesProcessor($this->env->skeleton(), $generatorFactory);
    }
}
