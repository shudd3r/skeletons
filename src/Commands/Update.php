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

use Shudd3r\PackageFiles\Commands;
use Shudd3r\PackageFiles\RuntimeEnv;
use Shudd3r\PackageFiles\Replacements;
use Shudd3r\PackageFiles\Template\Templates;
use Shudd3r\PackageFiles\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class Update implements Commands
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
        $cache            = new Replacements\TokenCache();
        $generatedFiles   = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $fileValidator    = new Processor\FilesProcessor\FilesValidator($generatedFiles, $templates, $cache);
        $fileGenerator    = new Processor\FilesProcessor\FilesGenerator($generatedFiles, $templates, $cache);
        $validationReader = new Replacements\Reader\ValidationReader($replacements, $this->env, $this->options);
        $updateReader     = new Replacements\Reader\UpdateReader($replacements, $this->env, $this->options);

        $metaDataExists      = new Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $packageSynchronized = new Precondition\SkeletonSynchronization($validationReader, $fileValidator);
        $validReplacements   = new Precondition\ValidReplacements($updateReader);
        $preconditions       = new Precondition\Preconditions($metaDataExists, $packageSynchronized, $validReplacements);

        $processTokens = new Command\TokenProcessor($updateReader, $fileGenerator, $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($updateReader, $this->env->metaData());
        $command       = new Command\CommandSequence($processTokens, $saveMetaData);

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }
}
