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


class Validate implements Factory
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
        $generatedFiles   = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $fileValidator    = $this->fileValidator($generatedFiles, $templates);
        $validationReader = new Reader\ValidationReader($replacements, $this->env, $this->options);

        $metaDataExists = new Command\Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $processTokens  = new Command\TokenProcessor($validationReader, $fileValidator, $this->env->output());

        return new Command\ProtectedCommand($processTokens, $metaDataExists, $this->env->output());
    }

    protected function fileValidator(Directory $generatedFiles, Templates $templates): Processor
    {
        $validators = new Processor\FileProcessors\FileValidators($templates);
        return new Processor\SkeletonFilesProcessor($generatedFiles, $validators);
    }
}
