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
use Shudd3r\PackageFiles\Templates;
use Shudd3r\PackageFiles\Replacements;
use Shudd3r\PackageFiles\Replacements\TokenCache;
use Shudd3r\PackageFiles\Processors;
use Shudd3r\PackageFiles\Processors\Processor;
use Shudd3r\PackageFiles\Commands\Command\DescribedCommand;
use Shudd3r\PackageFiles\Commands\Precondition\DescribedPrecondition;
use Shudd3r\PackageFiles\Environment\FileSystem\Directory;


abstract class Factory implements Commands
{
    protected RuntimeEnv   $env;
    protected Replacements $replacements;
    protected Directory    $generatedFiles;

    private Templates $templates;

    public function __construct(RuntimeEnv $env, Replacements $replacements, Templates $templates)
    {
        $this->env            = $env;
        $this->replacements   = $replacements;
        $this->generatedFiles = new Directory\ReflectedDirectory($env->package(), $env->skeleton());
        $this->templates      = $templates;
    }

    abstract public function command(array $options): Command;

    protected function filesValidator(TokenCache $tokenCache = null): Processor
    {
        return $this->fileProcessor(new Processors\FileValidators($this->env->output(), $tokenCache));
    }

    protected function filesGenerator(TokenCache $tokenCache = null): Processor
    {
        return $this->fileProcessor(new Processors\FileGenerators($this->env->output(), $tokenCache));
    }

    protected function commandInfo(string $message, Command $command): Command
    {
        return new DescribedCommand($command, $this->env->output(), $message);
    }

    protected function checkInfo(string $message, Precondition $precondition, bool $status = true): Precondition
    {
        return new DescribedPrecondition($precondition, $this->env->output(), $message, $status);
    }

    private function fileProcessor(Processors $processors): Processor
    {
        return new Processor\FilesProcessor($this->generatedFiles, $this->templates, $processors);
    }
}
