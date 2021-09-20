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
use Shudd3r\PackageFiles\Application\RuntimeEnv;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;
use Shudd3r\PackageFiles\Application\Processor;


class Initialize implements Command\Factory
{
    private RuntimeEnv $env;

    public function __construct(RuntimeEnv $env)
    {
        $this->env = $env;
    }

    public function command(array $options): Command
    {
        $tokenReader     = $this->env->replacements()->init($options);
        $generatedFiles  = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $backupDirectory = $this->env->backup();
        $output          = $this->env->output();

        $backupFiles   = new Command\BackupFiles($generatedFiles, $backupDirectory);
        $processTokens = new Command\TokenProcessor($tokenReader, $this->processor(), $output);
        $writeMetaData = new Command\WriteMetaData($tokenReader, $this->env->metaDataFile());

        $noMetaDataFile    = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Command\Precondition\CheckFilesOverwrite($generatedFiles, $backupDirectory);

        return new Command\ProtectedCommand(
            new Command\CommandSequence($backupFiles, $processTokens, $writeMetaData),
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
