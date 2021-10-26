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
use Shudd3r\PackageFiles\Setup\AppSetup;
use Shudd3r\PackageFiles\Replacements;
use Shudd3r\PackageFiles\Processors;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


class Validate implements Commands
{
    use DefineOutputMethods;

    private RuntimeEnv $env;
    private AppSetup   $setup;

    public function __construct(RuntimeEnv $env, AppSetup $setup)
    {
        $this->env   = $env;
        $this->setup = $setup;
    }

    public function command(array $options): Command
    {
        $replacements = $this->setup->replacements();
        $templates    = $this->setup->templates($this->env);

        $generatedFiles   = new Directory\ReflectedDirectory($this->env->package(), $this->env->skeleton());
        $validators       = new Processors\FileValidators();
        $fileValidator    = new Processors\Processor\FilesProcessor($generatedFiles, $templates, $validators);
        $validationReader = new Replacements\Reader\ValidationReader($replacements, $this->env, $options);

        $metaDataExists    = new Precondition\CheckFileExists($this->env->metaDataFile(), true);
        $validReplacements = new Precondition\ValidReplacements($validationReader);
        $checkMetaData     = new Precondition\Preconditions($metaDataExists, $validReplacements);

        $processTokens     = new Command\TokenProcessor($validationReader, $fileValidator, $this->env->output());

        return new Command\ProtectedCommand(
            $this->commandInfo('Checking skeleton synchronization', $processTokens),
            $this->checkInfo('Checking meta data status', $checkMetaData),
            $this->env->output()
        );
    }
}
