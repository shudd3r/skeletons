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
use Shudd3r\PackageFiles\Application\Token\TokenCache;
use Shudd3r\PackageFiles\Application\Processor;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class Update implements Factory
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
        $cache            = new TokenCache();
        $generatedFiles   = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $fileValidator    = $this->fileValidator($generatedFiles, $templates, $cache);
        $fileGenerator    = $this->fileGenerator($generatedFiles, $templates, $cache);
        $validationReader = new Reader\ValidationReader($replacements, $this->env, $this->options);
        $updateReader     = new Reader\UpdateReader($replacements, $this->env, $this->options);

        $metaDataExists      = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $packageSynchronized = new Command\Precondition\SkeletonSynchronization($validationReader, $fileValidator);
        $preconditions       = new Command\Precondition\Preconditions($metaDataExists, $packageSynchronized);

        $processTokens = new Command\TokenProcessor($updateReader, $fileGenerator, $this->env->output());
        $saveMetaData  = new Command\SaveMetaData($updateReader, $this->env->metaData());
        $command       = new Command\CommandSequence($processTokens, $saveMetaData);

        return new Command\ProtectedCommand($command, $preconditions, $this->env->output());
    }

    private function fileGenerator(Directory $generatedFiles, Templates $templates, TokenCache $cache): Processor
    {
        $generators = new Processor\FileProcessors\UpdatedFileGenerators($templates, $cache);
        return new Processor\SkeletonFilesProcessor($generatedFiles, $generators);
    }

    private function fileValidator(Directory $generatedFiles, Templates $templates, TokenCache $cache): Processor
    {
        $validators = new Processor\FileProcessors\CachingFileValidators($templates, $cache);
        return new Processor\SkeletonFilesProcessor($generatedFiles, $validators);
    }
}
