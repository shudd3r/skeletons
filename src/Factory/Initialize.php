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
use Shudd3r\PackageFiles\Application\Template\Templates;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class Initialize implements Factory
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
        $initialReader  = new Reader\InitialReader($replacements, $this->env, $this->options);
        $generatedFiles = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $fileGenerator  = new Processor\FileProcessors\NewFileGenerators($generatedFiles, $templates);

        $noMetaDataFile    = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), false);
        $noBackupOverwrite = new Command\Precondition\CheckFilesOverwrite($generatedFiles, $this->env->backup());
        $preconditions     = new Command\Precondition\Preconditions($noMetaDataFile, $noBackupOverwrite);

        $backupFiles   = new Command\BackupFiles($generatedFiles, $this->env->backup());
        $processTokens = new Command\TokenProcessor($initialReader, $fileGenerator, $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($initialReader, $this->env->metaData());
        $command       = new Command\CommandSequence($backupFiles, $processTokens, $saveMetaData);

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
